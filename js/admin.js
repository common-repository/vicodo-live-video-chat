;jQuery(function($) {
	$(document).ready(function() {
		$(".wrap-vicodo-lvc").each(function() {
			var wrapper = $(this);
			wrapper.find(".chosen-select").chosen();

			var showPages = wrapper.find("[name='vicodo_lvc_options[show_pages]']:checked").val();
			if(showPages == "specific" ) wrapper.find(".specific").css({height: "auto", overflow: "visible"});

			wrapper.find("[name='vicodo_lvc_options[show_pages]']").on("change", function(e) {
				wrapper.find(".specific").css({
					height: $(this).val() == "specific" ? "auto" : "0",
					overflow: $(this).val() == "specific" ? "visible" : "hidden"
				})
			});
		});
	});
});