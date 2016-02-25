<?php /** @var string[][] $groupedFields */ ?>
<div
        class="dms-form-fields"
        @if ($equalFields ?? false) data-equal-fields="{{ json_encode($equalFields) }}" @endif
        @if ($greaterThanFields ?? false) data-greater-than-fields="{{ json_encode($greaterThanFields) }}" @endif
        @if ($greaterThanOrEqualFields ?? false) data-greater-than-or-eqaul-fields="{{ json_encode($greaterThanOrEqualFields) }}" @endif
        @if ($lessThanFields ?? false) data-less-than-fields="{{ json_encode($lessThanFields) }}" @endif
        @if ($lessThanOrEqualFields ?? false) data-less-than-or-equal-fields="{{ json_encode($lessThanOrEqualFields) }}" @endif
>
    @foreach($groupedFields as $groupTitle => $fields)
        <fieldset class="dms-form-fieldset">
            @if(1|| count($groupedFields) > 1)<legend>{{ $groupTitle }}</legend>@endif
            @foreach($fields as $label => $field)
                <div class="form-group clearfix" data-field-name="{{ $field['name'] }}">
                    <div class="col-lg-2 col-md-3 col-sm-4">
                        <label data-for="{{ $field['name'] }}">{{ $label }}</label>
                    </div>
                    <div class="col-lg-10 col-md-9 col-sm-8">
                        {!! $field['content'] !!}
                        <div class="dms-validation-messages-container"></div>
                    </div>
                </div>
            @endforeach
        </fieldset>
    @endforeach
</div>