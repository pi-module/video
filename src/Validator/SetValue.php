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

class SetValue extends AbstractValidator
{
    const TAKEN = 'elementExists';

    /**
     * @var array
     */
    protected array $messageTemplates
        = [
            self::TAKEN => 'Please select element',
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
