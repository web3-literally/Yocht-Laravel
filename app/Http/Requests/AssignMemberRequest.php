<?php

namespace App\Http\Requests;

use App\CrewMember;
use App\Helpers\Owner;
use App\Models\Vessels\Vessel;
use App\Models\Vessels\VesselsCrew;
use App\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Sentinel;

class AssignMemberRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'user_id' => 'member',
            'position_id' => 'position',
        ];
    }

    /**
     * @param $id
     * @return Vessel
     * @throws \Exception
     */
    protected function loadVessel(int $id)
    {
        $owner = Owner::currentOwner();

        $vessel = Vessel::where('owner_id', $owner->id)->find($id);
        if (!$vessel) {
            throw new \Exception('Not found', 404);
        }

        return $vessel;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @throws \Exception
     * @return array
     */
    public function rules()
    {
        if (request('user_id')) {
            /** @var Vessel $vessel */
            $vessel = $this->loadVessel(request('boat_id'));

            $except = VesselsCrew::where('owner_id', $vessel->owner->id)->pluck('user_id')->all();
            $query = CrewMember::crewAccounts();
            if (Sentinel::getUser()->isCaptainAccount()) {
                // Captain can't assign a captain
                $roleTable = (new Role())->getTable();
                $query->where($roleTable . '.slug', '!=', 'captain');
            }
            $submembers = $query->where('parent_id', $vessel->owner->id)->whereNotIn('users.id', $except)->pluck('id')->all();

            return [
                'user_id' => 'required|' . Rule::in($submembers),
            ];
        }

        $roles = CrewMember::CREW_ROLES;
        if (Sentinel::getUser()->isCaptainAccount()) {
            // Captain can't assign a captain
            if (($key = array_search('captain', $roles)) !== false) {
                unset($roles[$key]);
            }
            $roles = ['crew'];
        }

        return [
            'role' => 'required|' . Rule::in($roles),
            'position_id' => 'required_if:role,crew|nullable|exists:positions,id',
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|min:3|max:191',
            'last_name' => 'required|min:3|max:191',
            'phone' => 'required|max:191|phone:AUTO,US',
        ];
    }

}
