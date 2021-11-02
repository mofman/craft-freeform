<?php

namespace Solspace\Freeform\Fields\Pro;

use Solspace\Freeform\Bundles\Form\SaveForm\SaveForm;
use Solspace\Freeform\Library\Composer\Components\AbstractField;
use Solspace\Freeform\Library\Composer\Components\Fields\Interfaces\InputOnlyInterface;
use Solspace\Freeform\Library\Composer\Components\Fields\Interfaces\NoStorageInterface;
use Solspace\Freeform\Library\Composer\Components\Fields\Interfaces\SingleValueInterface;
use Solspace\Freeform\Library\Composer\Components\Fields\Traits\SingleStaticValueTrait;

class SaveField extends AbstractField implements SingleValueInterface, InputOnlyInterface, NoStorageInterface
{
    use SingleStaticValueTrait;

    const POSITION_LEFT = 'left';
    const POSITION_CENTER = 'center';
    const POSITION_RIGHT = 'right';

    /** @var string */
    protected $label;

    /** @var string */
    protected $position = self::POSITION_RIGHT;

    /** @var string */
    protected $url;

    public function getPosition(): string
    {
        return $this->position;
    }

    public function getLabel(): string
    {
        return $this->translate($this->label);
    }

    /**
     * @return null|string
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function getType(): string
    {
        return self::TYPE_SAVE;
    }

    public function getInputHtml(): string
    {
        $attributes = $this->getCustomAttributes();
        $submitClass = $attributes->getInputClassOnly();
        $formSubmitClass = $this->getForm()->getPropertyBag()->get('saveClass', '');

        $submitClass = trim($submitClass.' '.$formSubmitClass);

        $this->addInputAttribute('class', $submitClass);

        return '<button '
            .$this->getInputAttributesString()
            .$this->getAttributeString('data-freeform-action', SaveForm::SAVE_ACTION)
            .$this->getAttributeString('type', 'submit')
            .$attributes->getInputAttributesAsString()
            .'>'
            .$this->getLabel()
            .'</button>';
    }
}