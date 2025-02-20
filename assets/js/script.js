jQuery( document ).ready(
	function ($) {
		$( "#select_all" ).on(
			"change",
			function () {
				$( ".link_checkbox" ).prop( "checked", $( this ).prop( "checked" ) );
			}
		);

		$( ".link_checkbox" ).on(
			"change",
			function () {
				if ($( ".link_checkbox:checked" ).length === $( ".link_checkbox" ).length) {
					$( "#select_all" ).prop( "checked", true );
				} else {
					$( "#select_all" ).prop( "checked", false );
				}
			}
		);
	}
);
