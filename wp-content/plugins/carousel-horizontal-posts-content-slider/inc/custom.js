jQuery(document).ready(function($) {

$("#foo1").carouFredSel({
	circular: false,
	responsive: true,
	infinite: false,
	auto 	: false,
	prev	: {	
		button	: "#foo1_prev",
		key		: "left"
	},
	next	: { 
		button	: "#foo1_next",
		key		: "right"
	},
	pagination	: "#foo1_pag",
		items: {
						
					//	height: '30%',	//	optionally resize item-height
						visible: {
							min: 1,
							max: 100000
						}
					}
});


});