<x-dynamic-component
    :component="$getFieldWrapperView()"
    :has-inline-label="$hasInlineLabel()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :state-path="$getStatePath()"
>
    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class(['filapress-media-info text-sm leading-6'])
        }}
    >
        <table>
        @foreach($getData() as $label=>$value)
            <tr>
                <th>{{ $label }}</th>
                <td>{{ $value }}</td>
            </tr>
            @endforeach
        </table>
    </div>
</x-dynamic-component>
