<?php

namespace App\Http\Controllers\Admin\Menu;

use App\Http\Requests\Menu\MenuRequest;
use App\Models\MenuItem;
use App\Repositories\MenuRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use Illuminate\Http\Request;
use App\Models\Menu;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class MenuController
 * @package App\Http\Controllers\Admin\Menu
 */
class MenuController extends InfyOmBaseController
{
    /**
     * @var MenuRepository
     */
    private $menuRepository;

    /**
     * MenuController constructor.
     * @param MenuRepository $menuRepository
     */
    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    /**
     * Display a listing of the Menu.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $this->menuRepository->pushCriteria(new RequestCriteria($request));

        $menus = $this->menuRepository->all();

        return view('admin.menus.index')->with('menus', $menus);
    }

    /**
     * Show the form for creating a new Menu.
     * @return mixed
     */
    public function create()
    {
        $types = Menu::getTypes();

        return view('admin.menus.menu_create', compact('types'));
    }

    /**
     * Store a newly created Menu in storage.
     * @param MenuRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(MenuRequest $request)
    {
        $input = $request->all();

        $menu = $this->menuRepository->create($input);

        Flash::success('Menu saved successfully.');

        return redirect()->route('admin.menus.edit.structure', $menu->id);
    }

    /**
     * Show the form for editing the specified Menu.
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function edit(int $id)
    {
        $menu = $this->menuRepository->findWithoutFail($id);

        if (empty($menu)) {
            Flash::error('Menu not found');

            return redirect(route('admin.menus.index'));
        }

        $types = Menu::getTypes();

        return view('admin.menus.menu_edit', compact('menu', 'types'));
    }

    /**
     * Update the specified Menu in storage.
     * @param int $id
     * @param MenuRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(int $id, MenuRequest $request)
    {
        $menu = $this->menuRepository->findWithoutFail($id);

        if (empty($menu)) {
            Flash::error('Menu not found');

            return redirect(route('admin.menus.index'));
        }

        $this->menuRepository->update($request->all(), $id);

        Flash::success('Menu updated successfully.');

        return redirect()->route('admin.menus.index');
    }

    /**
     * Show the form for editing the specified Menu structure.
     * @param int $id
     * @return Response
     */
    public function editStructure(int $id)
    {
        $menu = $this->menuRepository->findWithoutFail($id);

        if (empty($menu)) {
            Flash::error('Menu not found');

            return redirect(route('admin.menus.index'));
        }

        return view('admin.menus.menu_structure_edit', compact('menu'));
    }

    /**
     * @param int $menu
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function treeStructure(int $menu, Request $request)
    {
        $id = $request->get('id', '#');

        $menu = Menu::findOrFail($menu);

        $data = [];
        if ($id == '#') {
            $data[] = ['id' => 'root', 'text' => 'Menu', 'children' => boolval($menu->children->count()), 'type' => 'root', 'state' => ['opened' => true, 'disabled' => true]];
        } elseif($id == 'root') {
            $children = $menu->children()->get();
            if ($children) {
                foreach ($children as $item) {
                    $data[] = ['id' => $item->id, 'text' => $item->label, 'children' => boolval($item->children->count()), 'type' => $item->getItemType(), 'state' => ['opened' => false]];
                }
            }
        } else {
            $item = MenuItem::findOrFail($id);
            $children = $item->children()->get();
            if ($children) {
                foreach ($children as $child) {
                    $data[] = ['id' => $child->id, 'text' => $child->label, 'children' => boolval($child->children->count()), 'type' => $child->getItemType(), 'state' => ['opened' => false]];
                }
            }
        }

        return response()->json($data);
    }

    /**
     * Delete menu.
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id = null)
    {
        if (!($id > 2)) {
            return abort(404);
        }

        Menu::destroy($id);

        return redirect(route('admin.menus.index'))->with('success', 'Menu was successfully deleted.');

    }
}
