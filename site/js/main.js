(function() {
	
	$(".dark .logo, .dark .main-menu").addClass("animated bounceInDown");

	$(".homepage").addClass("animated fadeIn");
	$(".dark footer").addClass("animated fadeIn");

	$(".dropdown-toggle").click(function() {
		var expanded=$(this).attr("aria-expanded");
		if(expanded=="false") {
			$(".dropdown-menu").removeClass("visuallyhidden");
			$(this).attr("aria-expanded", "true");
		} else {
			$(".dropdown-menu").addClass("visuallyhidden");
			$(this).attr("aria-expanded", "false");
		}
		return false;
	});
	$("body").click(function() {
		$(".dropdown-menu").addClass("visuallyhidden");
		$(".dropdown-toggle").attr("aria-expanded", "false");
	});
	var expanded = false;
	$(".request, .result").click(function() {
		if(!expanded) {
			$(this).toggleClass('animate modal');
			$(this).attr("aria-expanded","true");

			$(this).find("a.btn").toggleClass("hidden");
			$(this).find("a.btn").attr("aria-hidden","false");

			$(this).find(".acceptform").toggleClass("visuallyhidden");

			$(this).find(".price").toggleClass("visuallyhidden");

			$(".modal-close").toggleClass('hidden');
			$(".modal-close").toggleClass('animate');
			$(".ti-arrows-corner").toggleClass('hidden');

			$("body").toggleClass('modal-open');
			expanded = true;
		}
	});
	$(".modal-close").click(function(ev) {
		$(".modal").attr("aria-expanded","false");

		$(".modal a.btn").toggleClass("hidden");
		$(".modal a.btn").attr("aria-hidden","true");

		$(".modal .acceptform").toggleClass("visuallyhidden");

		$(".modal .price").toggleClass("visuallyhidden");

		$(".modal-close").toggleClass('hidden');
		$(".modal-close").toggleClass('animate');
		$(" .ti-arrows-corner").toggleClass('hidden');
		$(".modal").toggleClass('animate modal');
		$("body").toggleClass('modal-open');
		ev.stopPropagation();
		expanded = false;
	});



	$(".add-subject").click(function() {
		var cur = parseInt($(this).attr("data-subjects"));
		cur++;
		var newSubjectGroup = '<div class="subjectgroup"><label for="subjects['+cur+'][name]" class="visuallyhidden">Materia:</label><input type="text" placeholder="Nome Materia" id="subjects['+cur+'][name]" name="subjects['+cur+'][name]" class="subject" tabindex="14"><label for="subjects['+cur+'][price]" class="visuallyhidden">Prezzo:</label><input type="text" placeholder="Prezzo/h" id="subjects['+cur+'][price]" name="subjects['+cur+'][price]" class="price" tabindex="'+cur+1+'"></div>';
		$(this).attr("data-subjects", ''+cur+'');
		$(newSubjectGroup).insertBefore(this);
		return false;
	});

})();
