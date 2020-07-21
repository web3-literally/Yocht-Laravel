<?php

namespace App\Http\Controllers\Admin\Menu;

use App\Http\Requests\Menu\MenuItemRequest;
use App\Http\Requests\Menu\UpdateMenuItemRequest;
use App\Repositories\MenuItemRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use App\Role;
use Illuminate\Http\Request;
use App\Models\MenuItem;
use Flash;
use Response;

class MenuItemController extends InfyOmBaseController
{
    /**
     * @var MenuItemRepository
     */
    private $menuItemRepository;

    /**
     * MenuItemController constructor.
     * @param MenuItemRepository $menuItemRepository
     */
    public function __construct(MenuItemRepository $menuItemRepository)
    {
        $this->menuItemRepository = $menuItemRepository;
    }

    /**
     * Store a newly created MenuItem in storage.
     * @param MenuItemRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(MenuItemRequest $request)
    {
        $input = $request->all();

        $item = $this->menuItemRepository->create($input);

        return response()->json([
            'success' => true,
            'id' => $item->id
        ]);
    }

    /**
     * @return mixed
     */
    protected function getGroups() {
        $items = Role::whereIn('slug', ['user', 'owner', 'marine', 'marinas_shipyards', 'land_services', 'captain'])->pluck('name', 'slug');
        $items->prepend('Guest', 'guest');

        return $items;
    }

    /**
     * Show the form for editing the specified MenuItem.
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        $id = $request->get('id');

        $item = MenuItem::findOrFail($id);

        $groups = $this->getGroups();

        return view('admin.menus.item.item_edit', compact('item', 'groups'));
    }

    /**
     * Update the specified MenuItem in storage.
     * @param $id
     * @param UpdateMenuItemRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdateMenuItemRequest $request)
    {
        $item = MenuItem::findOrFail($id);

        $fields = $request->except('label', 'parent');

        $collection = collect($fields['visible_for'] ?? []);
        if ($collection->count() == $this->getGroups()->count()) {
            $fields['visible_for'] = [];
        }

        $item->update($fields);

        Flash::success('Menu item updated successfully.');

        return redirect()->route('admin.menus.item.edit', ['id' => $item->id]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function rename(Request $request)
    {
        $id = $request->get('id');

        $item = MenuItem::findOrFail($id);

        $this->menuItemRepository->update($request->only('label'), $id);

        return response()->json([
            'success' => true,
            'id' => $item->id
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function move(Request $request)
    {
        $id = $request->get('id');

        $item = MenuItem::findOrFail($id);

        $this->menuItemRepository->update($request->only('parent', 'order'), $id);

        return response()->json([
            'success' => true,
            'id' => $item->id,
            'parent' => $item->parent
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $id = $request->get('id');

        $this->menuItemRepository->delete($id);

        return response()->json([
            'success' => true
        ]);
    }
}