<?php

namespace Appzcoder\LaravelAdmin\Controllers;

use App\Http\Controllers\Controller;
use Artisan;
use Illuminate\Http\Request;
use Response;
use Session;
use View;

class ProcessController extends Controller
{
    /**
     * Display generator.
     *
     * @return Response
     */
    public function getGenerator()
    {
        View::addNamespace('LaravelAdmin', __DIR__ . '/../views');

        return View::make('LaravelAdmin::generator');
    }

    /**
     * Process generator.
     *
     * @return Response
     */
    public function postGenerator(Request $request)
    {
        $commandArg = [];
        $commandArg['name'] = $request->crud_name;

        if ($request->has('fields')) {
            $fieldsArray = [];
            $x = 0;
            foreach ($request->fields as $field) {
                $required = ($request->fields_required[$x] == 1) ? '#required' : '';
                $fieldsArray[] = $field . '#' . $request->fields_type[$x] . $required;

                $x++;
            }

            $commandArg['--fields'] = implode(",", $fieldsArray);
        }

        if ($request->has('route')) {
            $commandArg['--route'] = $request->route;
        }

        if ($request->has('view_path')) {
            $commandArg['--view-path'] = $request->view_path;
        }

        if ($request->has('namespace')) {
            $commandArg['--namespace'] = $request->namespace;
        }

        if ($request->has('route_group')) {
            $commandArg['--route-group'] = $request->route_group;
        }

        try {
            Artisan::call('crud:generate', $commandArg);
        } catch (\Exception $e) {
            return Response::make($e->getMessage(), 500);
        }

        Session::flash('flash_message', 'Your CRUD has been generated. Just run migrate command.');

        return redirect('admin/generator');
    }
}
