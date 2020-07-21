<?php

namespace App\Http\Controllers;

/**
 * Class VesselsTemplateDocumentsController
 * @package App\Http\Controllers
 *
 * TODO: Refactoring needed
 */
class VesselsTemplateDocumentsController extends VesselsDocumentsController
{
    /**
     * @var string
     */
    protected $globalFolder = 'templates';

    /**
     * @var array
     */
    protected $validationRules = ['required', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:40000'];
}
