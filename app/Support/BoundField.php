<?php

namespace App\Support;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class BoundField implements Htmlable
{
    public $field;

    public $id_for_label;

    public $as_hidden;

    public $errors;

    public $label_tag;

    public function __construct(public string $name, public string $label, public string $type = 'text', public mixed $value = '', public bool $required = false, public array $options = [], array $errors = [])
    {
        $this->field = (object) ['required' => $required];
        $this->id_for_label = 'id_'.$name;
        $this->as_hidden = new HtmlString('<input type="hidden" name="'.e($name).'" value="'.e($value).'">');
        $this->errors = new HtmlString($errors ? '<ul class="errorlist"><li>'.implode('</li><li>', array_map('e', $errors)).'</li></ul>' : '');
        $this->label_tag = new HtmlString('<label for="'.$this->id_for_label.'">'.e($label).'</label>');
    }

    public function toHtml(): string
    {
        $attrs = ' name="'.e($this->name).'" id="'.$this->id_for_label.'"'.($this->required ? ' required' : '');
        if ($this->type === 'select') {
            $s = '<select'.$attrs.'>';
            foreach ($this->options as $v => $l) {
                $s .= '<option value="'.e($v).'"'.((string) $this->value === (string) $v ? ' selected' : '').'>'.e($l).'</option>';
            }

            return $s.'</select>';
        }
        if ($this->type === 'textarea') {
            return '<textarea'.$attrs.' rows="3" maxlength="2000">'.e($this->value).'</textarea>';
        }

        return '<input type="'.$this->type.'"'.$attrs.' value="'.e($this->type === 'checkbox' ? 'on' : $this->value).'"'.($this->type === 'checkbox' && $this->value ? ' checked' : '').($this->name === 'name' ? ' autocomplete="name"' : ($this->name === 'phone' ? ' autocomplete="tel" placeholder="+7 700 000 00 00"' : ($this->name === 'website' ? ' tabindex="-1" autocomplete="off"' : ''))).'>';
    }

    public function __toString()
    {
        return $this->toHtml();
    }
}
