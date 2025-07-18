<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProjectRequest;
use App\Models\Admin\ProjectCrud;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ProjectCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProjectCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(ProjectCrud::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/project');
        CRUD::setEntityNameStrings('project', 'projects');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('title')->type('text');
        CRUD::column('category')->type('text');
        CRUD::column('status')->type('select_from_array')->options([
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
        ]);
        CRUD::column('featured')->type('boolean');
        CRUD::column('project_date')->type('date');
        CRUD::column('created_at')->type('datetime');

        // Add filters
        CRUD::filter('category')
            ->type('dropdown')
            ->values(['web-development' => 'Web Development', 'mobile-app' => 'Mobile App', 'e-commerce' => 'E-commerce', 'api-development' => 'API Development'])
            ->whenActive(function ($value) {
                CRUD::addClause('where', 'category', $value);
            });

        CRUD::filter('status')
            ->type('dropdown')
            ->values(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])
            ->whenActive(function ($value) {
                CRUD::addClause('where', 'status', $value);
            });

        CRUD::filter('featured')
            ->type('simple')
            ->whenActive(function () {
                CRUD::addClause('where', 'featured', true);
            });
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ProjectRequest::class);

        // Basic Information
        CRUD::field('title')->type('text')->label('Project Title');
        CRUD::field('slug')->type('text')->hint('Leave empty to auto-generate from title');
        CRUD::field('short_description')->type('textarea')->label('Short Description');
        CRUD::field('description')->type('summernote')->label('Full Description');

        // Project Details
        CRUD::field('category')->type('select_from_array')->options([
            'web-development' => 'Web Development',
            'mobile-app' => 'Mobile App',
            'e-commerce' => 'E-commerce',
            'api-development' => 'API Development',
            'ui-ux-design' => 'UI/UX Design',
            'consulting' => 'Consulting',
        ])->allows_null(false);

        CRUD::field('technologies')->type('select2_multiple')->options([
            'Laravel' => 'Laravel',
            'Vue.js' => 'Vue.js',
            'React' => 'React',
            'Node.js' => 'Node.js',
            'PHP' => 'PHP',
            'JavaScript' => 'JavaScript',
            'TypeScript' => 'TypeScript',
            'Python' => 'Python',
            'MySQL' => 'MySQL',
            'PostgreSQL' => 'PostgreSQL',
            'Docker' => 'Docker',
            'AWS' => 'AWS',
            'Ionic' => 'Ionic',
            'Flutter' => 'Flutter',
        ])->pivot(true);

        // Media
        CRUD::field('featured_image')->type('upload')->upload(true)->disk('public');
        CRUD::field('images')->type('upload_multiple')->upload(true)->disk('public');

        // Links
        CRUD::field('demo_url')->type('url')->label('Demo URL');
        CRUD::field('github_url')->type('url')->label('GitHub URL');

        // Client Information
        CRUD::field('client_name')->type('text')->label('Client Name');
        CRUD::field('project_date')->type('date')->label('Project Date');

        // Status & Settings
        CRUD::field('status')->type('select_from_array')->options([
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
        ])->default('draft');

        CRUD::field('featured')->type('checkbox')->label('Featured Project');
        CRUD::field('sort_order')->type('number')->label('Sort Order')->default(0);

        // SEO
        CRUD::field('meta_title')->type('text')->label('Meta Title');
        CRUD::field('meta_description')->type('textarea')->label('Meta Description');
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // Execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        // Add created_by field
        $request->merge(['created_by' => auth()->id()]);

        // Insert item in the db
        $item = $this->crud->create($request->except(['_token', '_method']));
        $this->data['entry'] = $this->crud->entry = $item;

        // Show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // Save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        $this->crud->hasAccessOrFail('update');

        // Execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        // Add updated_by field
        $request->merge(['updated_by' => auth()->id()]);

        // Update the row in the db
        $item = $this->crud->update($request->get($this->crud->model->getKeyName()), $request->except(['_token', '_method']));
        $this->data['entry'] = $this->crud->entry = $item;

        // Show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // Save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }
}
