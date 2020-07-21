<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Country;
use App\Http\Requests\UserRequest;
use App\Mail\Register;
use App\Profile;
use App\User;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use File;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Redirect;
use Sentinel;
use URL;
use View;
use Yajra\DataTables\DataTables;
use Validator;
Use App\Mail\Restore;

/**
 * Class UsersController
 * @package App\Http\Controllers\Admin
 */
class UsersController extends BackEndController
{

    /**
     * Show a list of all the users.
     *
     * @return View
     */

    public function index()
    {
        $users = User::get();
        // Show the page
        return view('admin.users.index', compact('users'));
    }

    /*
     * Pass data through ajax call
     * @return mixed
     */
    public function data()
    {
        $users = User::get(['id', 'first_name', 'last_name', 'email','created_at']);

        return DataTables::of($users)
            ->editColumn('created_at',function(User $user) {
                return $user->created_at->diffForHumans();
            })
            ->addColumn('group',function($user){
                $group = [];
                $roles = $user->getRoles();
                $roles->each(function($item) use (&$group) {
                    $group[] = $item->name;
                });
                return implode(', ', $group);
            })
            ->addColumn('status',function($user){
                if($activation = Activation::completed($user)){
                    return 'Activated';
                } else
                    return 'Pending';
            })
            ->addColumn('actions',function($user) {
                $actions = '<a href='. route('admin.users.show', $user->id) .'><i class="livicon" data-name="info" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="view user"></i></a>
                            <a href='. route('admin.users.edit', $user->id) .'><i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="update user"></i></a>';
                if ((Sentinel::getUser()->id != $user->id) && ($user->id != 1)) {
                    $actions .= '<a href='. route('admin.users.confirm-delete', $user->id) .' data-id="'.$user->id.'" data-toggle="modal" data-target="#delete_confirm"><i class="livicon" data-name="user-remove" data-size="18" data-loop="true" data-c="#f56954" data-hc="#f56954" title="delete user"></i></a>';
                }
                return $actions;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /*
     * Pass deleted data through ajax call
     * @return mixed
     */
    public function deleted_data()
    {
        $users = User::onlyTrashed()->get(['id', 'first_name', 'last_name', 'email', 'deleted_at']);

        return DataTables::of($users)
            ->editColumn('deleted_at',function(User $user) {
                return $user->deleted_at->diffForHumans();
            })
            ->addColumn('group',function($user){
                $group = [];
                $roles = $user->getRoles();
                $roles->each(function($item) use (&$group) {
                    $group[] = $item->name;
                });
                return implode(', ', $group);
            })
            ->addColumn('actions',function($user) {
                $actions = '<a href="'. route('admin.restore.user', $user->id) .'"><i class="livicon" data-name="user-flag" data-c="#6CC66C" data-hc="#6CC66C" data-size="18"></i></a>';
                return $actions;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Create new user
     *
     * @return View
     */
    public function create()
    {
        // Get all the available groups
        $groups = Sentinel::getRoleRepository()->all();

        $countries = Country::getOptions();
        // Show the page
        return view('admin.users.create', compact('groups', 'countries'));
    }

    /**
     * User create form processing.
     *
     * @return Redirect
     */
    public function store(UserRequest $request)
    {
        //check whether use should be activated by default or not
        $activate = $request->get('activate') ? true : false;

        try {
            //upload image
            if ($request->hasFile('pic_file')) {
                $file = $request->file('pic_file');
                $extension = $file->extension();
                $uid = uniqid();
                $fileName = $uid . '.' . $extension;
                $destinationPath = public_path() . '/uploads/users/';

                $file->move($destinationPath, $fileName);
                $request['pic'] = $fileName;
            }

            // Register the user
            $user = Sentinel::register($request->except('_token', 'password_confirm', 'group', 'activate', 'pic_file'), $activate);

            //add user to 'User' group
            $role = Sentinel::findRoleById($request->get('group'));
            if ($role) {
                $role->users()->attach($user);
            }

            // Make a profile
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->save();

            // Activity log for New user create
            activity($user->full_name)
                ->performedOn($user)
                ->causedBy($user)
                ->log('New User Created by '.Sentinel::getUser()->full_name);

            $indexRoute = Redirect::route('admin.users.index');

            //check for activation and send activation mail if not activated by default
            if (!$request->get('activate')) {
                // Data to be used on the email view
                $data =[
                    'user_name' => $user->first_name .' '. $user->last_name,
                    'activationUrl' => URL::route('activate', [$user->id, Activation::create($user)->code])
                ];
                // Send the activation code through email
                try {
                    Mail::to($user->email)->send(new Register($data));
                } catch (\Throwable $e) {
                    // Redirect to the user creation page
                    $indexRoute->with('error', ' Failed ' . $e->getMessage());
                }
            }
            // Redirect to the home page with success menu
            return $indexRoute->with('success', trans('users/message.success.create'));
        } catch (LoginRequiredException $e) {
            $error = trans('admin/users/message.user_login_required');
        } catch (PasswordRequiredException $e) {
            $error = trans('admin/users/message.user_password_required');
        } catch (UserExistsException $e) {
            $error = trans('admin/users/message.user_exists');
        }

        // Redirect to the user creation page
        return Redirect::back()->withInput()->with('error', $error);
    }

    /**
     * User update.
     *
     * @param  int $id
     * @return View
     */
    public function edit(User $user)
    {
        // Get this user groups
        $userRoles = $user->getRoles()->pluck('name', 'id')->all();
        // Get a list of all the available groups
        $roles = Sentinel::getRoleRepository()->all();

        $status = Activation::completed($user);

        $countries = Country::getOptions();

        // Show the page
        return view('admin.users.edit', compact('user', 'roles', 'userRoles', 'countries', 'status'));
    }

    /**
     * User update form processing page.
     *
     * @param  User $user
     * @param UserRequest $request
     * @return Redirect
     */
    public function update(User $user, UserRequest $request)
    {
        try {
            $user->update($request->except('pic_file','password','password_confirm','groups','activate'));

            if ( !empty($request->password)) {
                $user->password = Hash::make($request->password);
            }

            // is new image uploaded?
            if ($request->hasFile('pic_file')) {
                $file = $request->file('pic_file');
                $extension = $file->extension();
                $uid = uniqid();
                $fileName = $uid . '.' . $extension;
                $destinationPath = public_path() . '/uploads/users/';

                $file->move($destinationPath, $fileName);
                $request['pic'] = $fileName;

                //delete old pic if exists
                $folders = File::glob($destinationPath . '*', GLOB_ONLYDIR);
                if ($folders) {
                    foreach($folders as $folder) {
                        $filePath = $destinationPath . basename($folder) . '/' . $user->pic;
                        if (File::exists($filePath)) {
                            File::delete($filePath);
                        }
                    }
                }
                $filePath = $destinationPath . $user->pic;
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }

                $user->pic = $fileName;
            }

            //save record
            $user->save();

            // Get the current user groups
            $userRoles = $user->roles()->pluck('id')->all();

            // Get the selected groups

            $selectedRoles = $request->get('groups');

            // Groups comparison between the groups the user currently
            // have and the groups the user wish to have.
            $rolesToAdd = array_diff($selectedRoles, $userRoles);
            $rolesToRemove = array_diff($userRoles, $selectedRoles);

            // Assign the user to groups

            foreach ($rolesToAdd as $roleId) {
                $role = Sentinel::findRoleById($roleId);
                $role->users()->attach($user);
            }

            // Remove the user from groups
            foreach ($rolesToRemove as $roleId) {
                $role = Sentinel::findRoleById($roleId);
                $role->users()->detach($user);
            }

            // Activate / De-activate user

            $status = $activation = Activation::completed($user);

            $editRoute = Redirect::route('admin.users.edit', $user);

            if ($request->get('activate') != $status) {
                if ($request->get('activate')) {
                    $activation = Activation::exists($user);
                    if ($activation) {
                        Activation::complete($user, $activation->code);
                    }
                } else {
                    //remove existing activation record
                    Activation::remove($user);
                    //add new record
                    Activation::create($user);
                    //send activation mail
                    $data=[
                        'user_name' =>$user->first_name .' '. $user->last_name,
                        'activationUrl' => URL::route('activate', [$user->id, Activation::exists($user)->code])
                    ];
                    // Send the activation code through email
                    try {
                        Mail::to($user->email)->send(new Register($data));
                    } catch (\Throwable $e) {
                        // Redirect to the user creation page
                        $editRoute->with('error', ' Failed ' . $e->getMessage());
                    }
                }
            }

            // Was the user updated?
            if ($user->save()) {
                // Prepare the success message
                $success = trans('users/message.success.update');
               //Activity log for user update
                activity($user->full_name)
                    ->performedOn($user)
                    ->causedBy($user)
                    ->log('User Updated by '.Sentinel::getUser()->full_name);
                // Redirect to the user page
                return $editRoute->with('success', $success);
            }

            // Prepare the error message
            $error = trans('users/message.error.update');
        } catch (UserNotFoundException $e) {
            // Prepare the error message
            $error = trans('users/message.user_not_found', compact('id'));

            // Redirect to the user management page
            return Redirect::route('admin.users.index')->with('error', $error);
        }

        // Redirect to the user page
        return $editRoute->withInput()->with('error', $error);
    }

    /**
     * Show a list of all the deleted users.
     *
     * @return View
     */
    public function getDeletedUsers()
    {
        // Grab deleted users
        //$users = User::onlyTrashed()->get();
        $users = [];

        // Show the page
        return view('admin.deleted_users', compact('users'));
    }


    /**
     * Delete Confirm
     *
     * @param   int $id
     * @return  View
     */
    public function getModalDelete($id)
    {
        $model = 'users';
        $confirm_route = $error = null;
        try {
            // Get user information
            $user = Sentinel::findById($id);

            // Check if we are not trying to delete ourselves
            if ($user->id === Sentinel::getUser()->id) {
                // Prepare the error message
                $error = trans('users/message.error.delete');

                return view('admin.layouts.modal_confirmation', compact('error', 'model', 'confirm_route'));
            }
        } catch (UserNotFoundException $e) {
            // Prepare the error message
            $error = trans('users/message.user_not_found', compact('id'));
            return view('admin.layouts.modal_confirmation', compact('error', 'model', 'confirm_route'));
        }
        $confirm_route = route('admin.users.delete', ['id' => $user->id]);
        return view('admin.layouts.modal_confirmation', compact('error', 'model', 'confirm_route'));
    }

    /**
     * Delete the given user.
     *
     * @param  int $id
     * @return Redirect
     */
    public function destroy($id)
    {
        try {
            // Get user information
            $user = Sentinel::findById($id);
            // Check if we are not trying to delete ourselves
            if ($user->id === Sentinel::getUser()->id) {
                // Prepare the error message
                $error = trans('admin/users/message.error.delete');
                // Redirect to the user management page
                return Redirect::route('admin.users.index')->with('error', $error);
            }
            // Delete the user
            //to allow soft deleted, we are performing query on users model instead of Sentinel model
            User::destroy($id);
            Activation::where('user_id',$user->id)->delete();
            // Prepare the success message
            $success = trans('users/message.success.delete');
            //Activity log for user delete
            activity($user->full_name)
                ->performedOn($user)
                ->causedBy($user)
                ->log('User deleted by '.Sentinel::getUser()->full_name);
            // Redirect to the user management page
            return Redirect::route('admin.users.index')->with('success', $success);
        } catch (UserNotFoundException $e) {
            // Prepare the error message
            $error = trans('admin/users/message.user_not_found', compact('id'));

            // Redirect to the user management page
            return Redirect::route('admin.users.index')->with('error', $error);
        }
    }

    /**
     * Restore a deleted user.
     *
     * @param  int $id
     * @return Redirect
     */
    public function getRestore($id)
    {
        try {
            // Get user information
            $user = User::withTrashed()->find($id);
            // Restore the user
            $user->restore();
            // create activation record for user and send mail with activation link
//            $data->user_name = $user->first_name .' '. $user->last_name;
//            $data->activationUrl = URL::route('activate', [$user->id, Activation::create($user)->code]);
            // Send the activation code through email
           $data=[
               'user_name' => $user->first_name .' '. $user->last_name,
            'activationUrl' => URL::route('activate', [$user->id, Activation::create($user)->code])
           ];
            Mail::to($user->email)
                ->send(new Restore($data));
            // Prepare the success message
            $success = trans('users/message.success.restored');
            activity($user->full_name)
                ->performedOn($user)
                ->causedBy($user)
                ->log('User restored by '.Sentinel::getUser()->full_name);
            // Redirect to the user management page
            return Redirect::route('admin.deleted_users')->with('success', $success);
        } catch (UserNotFoundException $e) {
            // Prepare the error message
            $error = trans('users/message.user_not_found', compact('id'));

            // Redirect to the user management page
            return Redirect::route('admin.deleted_users')->with('error', $error);
        }
    }

    /**
     * Display specified user profile.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        try {
            // Get the user information
            $user = Sentinel::findUserById($id);
            //get country name

            $countries = Country::getAll();
            if ($user->country) {
                $user->country = ($countries[$user->country] ?? '');
            }
        } catch (UserNotFoundException $e) {
            // Prepare the error message
            $error = trans('users/message.user_not_found', compact('id'));
            // Redirect to the user management page
            return Redirect::route('admin.users.index')->with('error', $error);
        }
        // Show the page
        return view('admin.users.show', compact('user'));

    }

    public function passwordreset( Request $request)
    {
        $id = $request->id;
        $user = Sentinel::findUserById($id);
        $password = $request->get('password');
        $user->password = Hash::make($password);
        $user->save();
    }

    public function lockscreen($id){

        if (Sentinel::check()) {
            $user = Sentinel::findUserById($id);
            return view('admin.lockscreen',compact('user'));
        }
        return view('admin.login');
    }

    public function postLockscreen(Request $request){
        $password = Sentinel::getUser()->password;
        if(Hash::check($request->password,$password)){
            return 'success';
        } else{
            return 'error';
        }
    }
}
