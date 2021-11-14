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

class NameDuplicate extends AbstractValidator
{
    const TAKEN = 'nameExists';

    /**
     * @var array
     */
    protected array $messageTemplates
        = [
            self::TAKEN => 'This name already exists',
        ];

    protected array $options
        = [
            'module', 'table',
        ];

    /**
     * Name validate
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
            $where = ['name' => $value];
            if (!empty($context['id'])) {
                $where['id <> ?'] = $context['id'];
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
