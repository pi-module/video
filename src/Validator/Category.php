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

class Category extends AbstractValidator
{
    const TAKEN = 'categoryExists';

    /**
     * @var array
     */
    protected array $messageTemplates
        = [
            self::TAKEN => 'Please select category',
        ];

    protected array $options = [];

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
        $value = intval($value);
        if ($value > 0) {
            return true;
        } else {
            $this->error(static::TAKEN);
            return false;
        }
    }
}
