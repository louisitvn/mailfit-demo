					<script>
						@foreach (['danger', 'warning', 'success', 'info', 'error', 'warning'] as $msg)
                            
							@if(Session::has('alert-' . $msg))
                            
								$(document).ready(function() {
									// Success alert
									swal({
										title: "{!! Session::get('alert-' . $msg) !!}",
										text: "",
										confirmButtonColor: "#00695C",
										type: "{{ $msg }}",
										allowOutsideClick: true,
										confirmButtonText: "{{ trans('messages.ok') }}",
										customClass: "swl-{{ $msg }}"
									});								
									
								});									
							
							@endif
						@endforeach
						
					</script>