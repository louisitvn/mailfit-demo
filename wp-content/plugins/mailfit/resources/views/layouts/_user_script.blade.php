<script>
	$.cookie('last_language_code', '{{ Auth::user()->getLanguageCode() }}');
</script>