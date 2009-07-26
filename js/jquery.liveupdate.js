jQuery.fn.liveUpdate = function(list){
	list = jQuery(list);

	if ( list.length ) {
		var rows = list.children('.jobItem'),
			cache = rows.map(function(){
				return this.innerHTML.toLowerCase();
			});

		this
			.keyup(filter).keyup()
			.parents('form').submit(function(){
				return false;
			});

	}
		
	return this;
		
	function filter(){
		var term = jQuery.trim( jQuery(this).val().toLowerCase() ), scores = [];
		if ( !term ) {
			rows.removeClass('filtered');
		} else {
			rows.addClass('filtered');
			cache.each(function(i){
                var score =  this.indexOf(term) >= 0 ? 1 : 0;
				if (score > 0) { scores.push([score, i]); }
			});
			jQuery.each(scores.sort(function(a, b){return b[0] - a[0];}), function(){
                if (jQuery(rows[ this[1] ]).hasClass('visible')) {
    				jQuery(rows[ this[1] ]).removeClass('filtered');
                }
			});
		}
	}
};
