<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Renderer\Form\Field;

use Dms\Common\Structure\DateTime\Form\DateOrTimeRangeType;
use Dms\Common\Structure\FileSystem\Form\FileUploadType;
use Dms\Common\Structure\FileSystem\Form\ImageUploadType;
use Dms\Core\Form\Field\Type\FieldType;
use Dms\Core\Form\Field\Type\InnerFormType;
use Dms\Core\Form\IField;
use Dms\Core\Form\IFieldType;
use Dms\Web\Laravel\Renderer\Form\FormRenderer;

/**
 * The inner-form field renderer
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class InnerFormFieldRenderer extends BladeFieldRenderer
{
    /**
     * Gets the expected class of the field type for the field.
     *
     * @return string
     */
    public function getFieldTypeClass() : string
    {
        return InnerFormType::class;
    }

    /**
     * @param IField     $field
     * @param IFieldType $fieldType
     *
     * @return bool
     */
    protected function canRender(IField $field, IFieldType $fieldType) : bool
    {
        return !$fieldType->has(FieldType::ATTR_OPTIONS)
        && !($fieldType instanceof DateOrTimeRangeType)
        && !($fieldType instanceof FileUploadType);
    }

    /**
     * @param IField     $field
     * @param IFieldType $fieldType
     *
     * @return string
     */
    protected function renderField(IField $field, IFieldType $fieldType) : string
    {
        /** @var InnerFormType $fieldType */
        $formWithArrayFields = $fieldType->getInnerArrayForm($field->getName());
        $formRenderer = new FormRenderer($this->fieldRendererCollection);


        return $this->renderView(
            $field,
            'dms::components.field.inner-form.input',
            [],
            [
                'formContent' => $formRenderer->renderFields($formWithArrayFields),
            ]
        );
    }

    /**
     * @param IField     $field
     * @param IFieldType $fieldType
     *
     * @return string
     */
    protected function renderFieldValue(IField $field, $value, IFieldType $fieldType) : string
    {
        /** @var InnerFormType $fieldType */
        $formWithArrayFields = $fieldType->getInnerArrayForm($field->getName());
        $formRenderer = new FormRenderer($this->fieldRendererCollection);

        return $this->renderValueViewWithNullDefault(
            $field, $value,
            'dms::components.field.inner-form.value',
            [
                'formContent' => $formRenderer->renderFields($formWithArrayFields),
            ]
        );
    }
}