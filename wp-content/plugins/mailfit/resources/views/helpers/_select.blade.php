<select name="{{ $name }}" class="select-search{{ $classes }} {{ isset($class) ? $class : "" }}" {{ isset($multiple) && $multiple == true ? "multiple='multiple'" : "" }}>
	@if (isset($include_blank))
		<option value="">{{ $include_blank }}</option>
	@endif
	@foreach($options as $option)
		<option{{ in_array($option['value'], explode(",", $value)) ? " selected" : "" }} value="{{ $option['value'] }}">{{ $option['text'] }}</option>
	@endforeach
</select>