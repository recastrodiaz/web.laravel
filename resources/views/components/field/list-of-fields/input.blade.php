<?php /** @var \Dms\Web\Laravel\Renderer\Form\IFieldRenderer $fieldRenderer */ ?>
<?php /** @var \Dms\Core\Form\IField $elementField */ ?>
<?php $elementField = $elementField->withName($name, str_singular($label)); ?>
<ul
        class="list-group dms-field-list"
        @if($exactElements !== null)
        data-min-elements="{{ $exactElements }}"
        data-max-elements="{{ $exactElements }}"
        @else
        @if($minElements !== null) data-min-elements="{{ $minElements }}" @endif
        @if($maxElements !== null) data-max-elements="{{ $maxElements }}" @endif
        @endif
>
    <li class="list-group-item hidden field-list-template clearfix dms-no-validation dms-form-no-submit">
        <div class="row">
            <div class="col-xs-10 col-md-11 field-list-input">
                {{ $fieldRenderer->render($elementField->withName($name . '[::index::]')->withInitialValue(null)) }}
            </div>
            <div class="col-xs-2 col-md-1 field-list-button-container">
                <button class="btn btn-danger btn-block btn-remove-field" tabindex="-1"><span class="fa fa-times"></span></button>
            </div>
        </div>
    </li>

    @if ($value !== null)
        @foreach (array_values($value) as $key => $valueElement)
            <li class="list-group-item field-list-item clearfix">
                <div class="row">
                    <div class="col-xs-10 col-md-11 field-list-input">
                        {!! $fieldRenderer->render($elementField->withName($name . '[' . $key . ']')->withInitialValue($valueElement)) !!}
                    </div>
                    <div class="col-xs-2 col-md-1 field-list-button-container">
                        <button class="btn btn-danger btn-block btn-remove-field" tabindex="-1"><span class="fa fa-times"></span></button>
                    </div>
                </div>
            </li>
        @endforeach
    @endif

    <li class="list-group-item field-list-add">
        <button type="button" class="btn btn-success btn-add-field">Add <span class="fa fa-plus"></span></button>
    </li>
</ul>