<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt BSD 3-Clause License
 */

/**
 * @author Somayeh Karami <somayeh.karami@gmail.com>
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
namespace Module\Video\Form;

use Pi;
use Pi\Form\Form as BaseForm;

class CategoryForm extends BaseForm
{
    protected $thumbUrl = '';

    public function __construct($name = null, $option = array())
    {
        $this->category = array(0 => '');
        $this->thumbUrl = $option['thumbUrl'];
        $this->removeUrl = empty($option['removeUrl']) ? '' : $option['removeUrl'];
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new CategoryFilter;
        }
        return $this->filter;
    }

    public function init()
    {
        // id
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        // parent
        $this->add(array(
            'name' => 'parent',
            'type' => 'Module\Video\Form\Element\Category',
            'options' => array(
                'label' => __('Parent Category'),
                'category' => $this->category,
            ),
            'attributes' => array(
                'size' => 1,
                'multiple' => 0,
            ),
        ));
        // title
        $this->add(array(
            'name' => 'title',
            'options' => array(
                'label' => __('Title'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
                'required' => true,
            )
        ));
        // slug
        $this->add(array(
            'name' => 'slug',
            'options' => array(
                'label' => __('slug'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',

            )
        ));
        // text_description
        $this->add(array(
            'name' => 'text_description',
            'options' => array(
                'label' => __('Description'),
                'editor' => 'html',
            ),
            'attributes' => array(
                'type' => 'editor',
                'description' => '',
            )
        ));
        // display_order
        $this->add(array(
            'name' => 'display_order',
            'options' => array(
                'label' => __('Display order'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
                'required' => false,
            )
        ));
        // status
        $this->add(array(
            'name' => 'status',
            'type' => 'select',
            'options' => array(
                'label' => __('Status'),
                'value_options' => array(
                    1 => __('Published'),
                    2 => __('Pending review'),
                    3 => __('Draft'),
                    4 => __('Private'),
                ),
            ),
            'attributes' => array(
                'required' => true,
            )
        ));
        // Image
        if ($this->thumbUrl) {
            $this->add(array(
                'name' => 'imageview',
                'type' => 'Module\Video\Form\Element\Image',
                'options' => array(//'label' => __('Image'),
                ),
                'attributes' => array(
                    'src' => $this->thumbUrl,
                ),
            ));
            $this->add(array(
                'name' => 'remove',
                'type' => 'Module\Video\Form\Element\Remove',
                'options' => array(
                    'label' => __('Remove image'),
                ),
                'attributes' => array(
                    'link' => $this->removeUrl,
                ),
            ));
            $this->add(array(
                'name' => 'image',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ));
        } else {
            $this->add(array(
                'name' => 'image',
                'options' => array(
                    'label' => __('Image'),
                ),
                'attributes' => array(
                    'type' => 'file',
                    'description' => '',
                )
            ));
        }
        // extra
        $this->add(array(
            'name' => 'extra_seo',
            'type' => 'fieldset',
            'options' => array(
                'label' => __('SEO options'),
            ),
        ));
        // seo_title
        $this->add(array(
            'name' => 'seo_title',
            'options' => array(
                'label' => __('SEO Title'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '2',
                'cols' => '40',
                'description' => __('Between 10 to 70 character'),
            )
        ));
        // seo_keywords
        $this->add(array(
            'name' => 'seo_keywords',
            'options' => array(
                'label' => __('SEO Keywords'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '2',
                'cols' => '40',
                'description' => __('Between 5 to 10 words'),
            )
        ));
        // seo_description
        $this->add(array(
            'name' => 'seo_description',
            'options' => array(
                'label' => __('SEO Description'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '3',
                'cols' => '40',
                'description' => __('Between 80 to 160 character'),
            )
        ));
        // Save
        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'value' => __('Submit'),
            )
        ));
    }
}