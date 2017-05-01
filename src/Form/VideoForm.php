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

class VideoForm  extends BaseForm
{
    public function __construct($name = null, $option = array())
    {
        $this->category = array(0 => ' ');
        $this->thumbUrl = empty($option['thumbUrl']) ? '' : $option['thumbUrl'];
        $this->removeUrl = empty($option['removeUrl']) ? '' : $option['removeUrl'];
        $this->option = $option;
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new VideoFilter($this->option);
        }
        return $this->filter;
    }

    public function init()
    {
        // extra_general
        $this->add(array(
            'name' => 'extra_general',
            'type' => 'fieldset',
            'options' => array(
                'label' => __('General options'),
            ),
        ));
        // id
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        // status
        if ($this->option['side'] == 'admin') {
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
                        5 => __('Delete'),
                    ),
                ),
                'attributes' => array(
                    'required' => true,
                    'class' => 'video-from-status',
                )
            ));
        } else {
            $this->add(array(
                'name' => 'status',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ));
        }
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
        if ($this->option['side'] == 'admin') {
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
        } else {
            $this->add(array(
                'name' => 'slug',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ));
        }
        // text_summary
        $this->add(array(
            'name' => 'text_summary',
            'options' => array(
                'label' => __('Summary'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '5',
                'cols' => '40',
                'description' => __('Keep summery short, 2 or 3 lines'),
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
        // category
        $this->add(array(
            'name' => 'category',
            'type' => 'Module\Video\Form\Element\Category',
            'options' => array(
                'label' => __('Category'),
                'category' => '',
            ),
            'attributes' => array(
                'required' => true,
            )
        ));
        // category_main
        $this->add(array(
            'name' => 'category_main',
            'type' => 'Module\Video\Form\Element\Category',
            'options' => array(
                'label' => __('Main category'),
                'category' => $this->category,
            ),
            'attributes' => array(
                'size' => 1,
                'multiple' => 0,
                'description' => __('Use for breadcrumbs ,mobile app and attribute'),
                'required' => true,
            ),
        ));
        // brand
        if ($this->option['brand_system']) {
            $this->add(array(
                'name' => 'brand',
                'type' => 'Module\Video\Form\Element\Brand',
                'options' => array(
                    'label' => __('Brand'),
                    'category' => $this->category,
                ),
                'attributes' => array(
                    'size' => 1,
                    'multiple' => 0,
                    'required' => false,
                ),
            ));
        }
        // Image
        if ($this->thumbUrl) {
            $this->add(array(
                'name' => 'imageview',
                'type' => 'Module\Video\Form\Element\Image',
                'options' => array(
                    //'label' => __('Image'),
                ),
                'attributes' => array(
                    'src' => $this->thumbUrl,
                ),
            ));
            if ($this->option['side'] == 'admin') {
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
            }
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
        // Video
        if ($this->option['side'] == 'admin') {
            // extra_video
            $this->add(array(
                'name' => 'extra_video',
                'type' => 'fieldset',
                'options' => array(
                    'label' => __('Video file'),
                ),
            ));
            /* // video_type
            $this->add(array(
                'name' => 'video_type',
                'options' => array(
                    'label' => __('Type'),
                ),
                'attributes' => array(
                    'type' => 'description',
                    'description' => $this->option['video_type'],
                )
            ));
            // video_extension
            $this->add(array(
                'name' => 'video_extension',
                'options' => array(
                    'label' => __('Extension'),
                ),
                'attributes' => array(
                    'type' => 'description',
                    'description' => $this->option['video_extension'],
                )
            ));
            // video_size
            $this->add(array(
                'name' => 'video_size',
                'options' => array(
                    'label' => __('Size'),
                ),
                'attributes' => array(
                    'type' => 'description',
                    'description' => $this->option['video_size'],
                )
            )); */
            // video_duration
            $this->add(array(
                'name' => 'video_duration',
                'options' => array(
                    'label' => __('Duration ( second )'),
                ),
                'attributes' => array(
                    'type' => 'text',
                    'description' => '',
                )
            ));
        } else {
            // video_duration
            $this->add(array(
                'name' => 'video_duration',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ));
        }
        // Price
        if ($this->option['side'] == 'admin') {
            switch ($this->option['sale_video']) {
                case 'package':
                    // sale_type
                    $this->add(array(
                        'name' => 'sale_type',
                        'type' => 'select',
                        'options' => array(
                            'label' => __('Sale video'),
                            'value_options' => array(
                                'free' => __('Free'),
                                'paid' => __('Paid'),
                            ),
                        ),
                        'attributes' => array(
                            'description' => __('If check it, users should buy package to watch this video'),
                        )
                    ));
                    break;

                case 'single':
                    // sale_type
                    $this->add(array(
                        'name' => 'sale_type',
                        'type' => 'select',
                        'options' => array(
                            'label' => __('Sale video'),
                            'value_options' => array(
                                'free' => __('Free'),
                                'paid' => __('Paid'),
                            ),
                        ),
                        'attributes' => array(
                            'description' => __('If check it and put price, users should pay to watch this video'),
                        )
                    ));
                    // sale_price
                    $this->add(array(
                        'name' => 'sale_price',
                        'options' => array(
                            'label' => __('Sale Video price'),
                        ),
                        'attributes' => array(
                            'type' => 'text',
                            'description' => '',
                        )
                    ));
                    break;
            }
        }
        // Seo
        if ($this->option['side'] == 'admin') {
            // extra_seo
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
            // tag
            if (Pi::service('module')->isActive('tag')) {
                $this->add(array(
                    'name' => 'tag',
                    'type' => 'tag',
                    'options' => array(
                        'label' => __('Tags'),
                    ),
                    'attributes' => array(
                        'id' => 'tag',
                        'description' => __('Use `|` as delimiter to separate tag terms'),
                    )
                ));
            }
        } else {
            // seo_title
            $this->add(array(
                'name' => 'seo_title',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ));
            // seo_keywords
            $this->add(array(
                'name' => 'seo_keywords',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ));
            // seo_description
            $this->add(array(
                'name' => 'seo_description',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ));
            // tag
            $this->add(array(
                'name' => 'tag',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ));
        }
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