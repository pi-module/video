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

namespace Module\Video\Validator;

use Pi;
use Laminas\Validator\AbstractValidator;

class SlugDuplicate extends AbstractValidator
{
    const TAKEN = 'slugExists';

    /**
     * @var array
     */
    protected array $messageTemplates
        = [
            self::TAKEN => 'This slug already exists',
        ];

    protected array $options
        = [
            'module', 'table', 'id'
        ];

    /**
     * Slug validate
     *
     * @param mixed $value
     * @param array|null $context
     *
     * @return boolean
     */
    public function isValid($value, array $context = null): bool
    {
        $this->setValue($value);
        if (null !== $value) {
            $where = ['slug' => $value];
            if (!empty($this->options['id'])) {
                $where['id <> ?'] = $this->options['id'];
            }
            $rowSet = Pi::model($this->options['table'], $this->options['module'])->select($where);
            if ($rowSet->count()) {
                $this->error(static::TAKEN);
                return false;
            }
        }
        return true;
    }
}
