<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\Video\Form;

use Pi;
use Pi\Form\Form as BaseForm;

class AdminSearchForm extends BaseForm
{
    public function __construct($name = null, $option = [])
    {
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new AdminSearchFilter;
        }
        return $this->filter;
    }

    public function init()
    {
        // title
        $this->add(
            [
                'name'       => 'title',
                'options'    => [
                    'label' => __('Title'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                    'placeholder' => __('Title'),
                ],
            ]
        );
        // category
        $this->add(
            [
                'name'       => 'category',
                'type'       => 'Module\Video\Form\Element\Category',
                'options'    => [
                    'label' => __('Category'),
                    //'category' => $this->category,
                ],
                'attributes' => [
                    'size'     => 1,
                    'multiple' => 0,
                ],
            ]
        );
        // brand
        $this->add(
            [
                'name'       => 'brand',
                'type'       => 'Module\Video\Form\Element\Brand',
                'options'    => [
                    'label' => __('Brand'),
                    //'category' => $this->category,
                ],
                'attributes' => [
                    'size'     => 1,
                    'multiple' => 0,
                ],
            ]
        );
        // status
        $this->add(
            [
                'name'    => 'status',
                'type'    => 'select',
                'options' => [
                    'label'         => __('Status'),
                    'value_options' => [
                        '' => __('All status'),
                        1  => __('Published'),
                        2  => __('Pending review'),
                        3  => __('Draft'),
                        4  => __('Private'),
                        5  => __('Delete'),
                    ],
                ],
            ]
        );
        // recommended
        $this->add(
            [
                'name'    => 'recommended',
                'type'    => 'select',
                'options' => [
                    'label'         => __('Recommended'),
                    'value_options' => [
                        '' => __('All'),
                        1  => __('Recommended'),
                    ],
                ],
            ]
        );
        // Save
        $this->add(
            [
                'name'       => 'submit',
                'type'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => __('Search'),
                ],
            ]
        );
    }
}