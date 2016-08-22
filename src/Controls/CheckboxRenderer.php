<?php

namespace Instante\Bootstrap3Renderer\Controls;

use Instante\Bootstrap3Renderer\RenderModeEnum;
use Nette\Forms\IControl;
use Nette\Utils\Html;

class CheckboxRenderer extends DefaultControlRenderer
{

    /**
     * div.form-group
     * [div.col-{ScreenSize}-offset-{LabelColumns}.col-{ScreenSize}-{InputColumns}]
     *     div.checkbox
     *         label
     *             input type="checkbox"
     *             ...Label
     *
     * @param IControl $control
     * @return Html
     */
    public function renderPair(IControl $control)
    {
        $r = $this->bootstrapRenderer;
        $pair = clone $r->getPrototypes()->pair;

        $label = $this->renderCheckboxInLabel($control);
        $cb = Html::el('div', ['class' => 'checkbox']);
        $cb->addHtml($label);
        if ($r->getRenderMode() === RenderModeEnum::HORIZONTAL) {
            // wrap in bootstrap columns
            $wrapper = Html::el('div')
                ->appendAttribute('class', $r->getColumnsClass($r->getInputColumns()))
                ->appendAttribute('class', $r->getOffsetClass($r->getLabelColumns()))
                ->addHtml($cb);
        } else {
            $wrapper = $cb;
        }

        $pair->getPlaceholder('control')->addHtml($wrapper);

        $pair->getPlaceholder('errors')->addHtml($r->renderControlErrors($control));
        $pair->getPlaceholder('description')->addHtml($r->renderControlDescription($control));
        return $pair;
    }

    /** @inheritdoc */
    public function renderCheckboxInLabel(IControl $control)
    {
        $label = $this->renderLabel($control);
        $controlHtml = $this->renderControl($control);
        $label->insert(0, $controlHtml);
        return $label;
    }

    /** @inheritdoc */
    public function renderControl(IControl $control, $renderedDescription = FALSE)
    {
        if (!method_exists($control, 'getControlPart')) {
            return Html::el();
        }
        $r = $this->bootstrapRenderer;
        /** @var Html $el */
        $el = $control->getControlPart(); // the dirty Nette way to get <input type=checkbox> only from checkbox
        if ($el instanceof Html) {
            if ($renderedDescription && $r->getControlDescription($control) !== NULL) {
                $el->setAttribute('aria-describedby', $r->getDescriptionId($control));
            }
        } else {
            $el = Html::el()->addHtml($el);
        }
        return $el;
    }
}