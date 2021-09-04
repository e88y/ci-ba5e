<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This helper contains abstract enumeration classes extending BASE_Enum
 * 
 * @author Marco Eberhardt
 * @category helper
 * @package application\helper\enum_helper
 * @version 1.2
 */

/**
 * @package application\helpers\enum_helper
 * @see resources/css/global.css
 */
abstract class E_BG_COLOR extends BASE_Enum
{
	const GREY			= "bg-grey";
	const BLACK			= "bg-black";
	const RED			= "bg-red";
	const YELLOW		= "bg-yellow";
	const AQUA			= "bg-aqua";
	const BLUE			= "bg-blue";
	const LIGHT_BLUE	= "bg-light-blue";
	const GREEN			= "bg-green";
	const NAVY			= "bg-navy";
	const TEAL			= "bg-teal";
	const OLIVE			= "bg-olive";
	const LIME			= "bg-lime";
	const ORANGE		= "bg-orange";
	const FUCHSIA		= "bg-fuchsia";
	const PURPLE		= "bg-purple";
	const MAROON		= "bg-maroon";
	const WHITE			= "bg-white";
	const SILVER		= "bg-silver";
}

/**
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 * @final
 */
abstract class E_BUTTON_TYPES extends BASE_Enum
{
	const BUTTON	= "button";
	const SUBMIT	= "submit";
	const RESET		= "reset";
}

/**
 * HTTP 1.1. Allowed values for META Cache-Control
 * 	= PUBLIC | PRIVATE | NO-CACHE | NO-STORE. 
 * 
 * @see RFC 7234
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_CACHE_CONTROL extends BASE_Enum
{
	const MUST_REVALIDATE	= "must-revalidate";
	const NO_CACHE			= "no-cache";
	const NO_STORE			= "no-store";
	const NO_TRANSFORM		= "no-transform";
	const PUB				= "public";
	const PRIV				= "private";
	const PROXY_REVALIDATE	= "proxy-revalidate";
	
	static function MAX_AGE ($age){
		return "max-age=".$age;
	}
	static function MAX_SECONDS ($sec){
		return "max-seconds=".$sec;
	}
	static function NO_CACHING(){
		return self::NO_CACHE.",".self::NO_STORE.",".self::MUST_REVALIDATE;
	}
}

/**
 * @category helper
 * @package application\helpers\enum_helper
 * @final
 */
abstract class E_CHECKED extends BASE_Enum
{
	const YES	= true;
	const NO	= false;
}

/**
 * @category helper
 * @package application\helpers\enum_helper
 * @final
 */
abstract class E_COLLAPSEABLE extends BASE_Enum
{
	const YES	= true;
	const NO	= false;
}

/**
 * Bootstrap color palette
 * 
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 * @final
 */
abstract class E_COLOR extends BASE_Enum
{
	const STANDARD	= "default";
	const PRIMARY	= "primary";
	const SUCCESS 	= "success";
	const INFO		= "info";
	const WARNING   = "warning";
	const DANGER	= "danger";
}

/**
 * The usual default method within a controller.
 * 
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_CONTROLLER_METHOS extends BASE_Enum
{
	const ALL = array(
		"edit"=>array(
			"icon"=>'<i class="fa fa-pencil"></i>'
		),
		"create"=>array(
			"icon"=>'<i class="fa fa-plus"></i>'
		),
		"new"=>array(
				"icon"=>'<i class="fa fa-plus"></i>'
		),
		"remove"=>array(
			"icon"=>'<i class="fa fa-trash"></i>'
		),
		"show"=>array(
			"icon"=>'<i class="fa fa-table"></i>'
		),
		"settings"=>array(
			"icon"=>'<i class="fa fa-wrench"></i>'
		)
	);
}

/**
 * possible "data-backdrop"-options for dialogs
 *
 * @category BASE_Enum
 * @package application\helpers\enum_helper
 * @final
 */
abstract class E_DATA_BACKDROPS extends BASE_Enum
{
	const BACKDROP_ENABLED	= 'true';
	const BACKDROP_DISABLED	= 'false';
	const BACKDROP_STATIC	= 'static';		// dialog cannot be closed by clicking the background
}

/**
 * @category BASE_Enum
 * @package application\helpers\enum_helper
 * @final
 */
abstract class E_DISMISSABLE extends BASE_Enum
{
	const YES 	= true;
	const NO	= false;		
}

/**
 * @category helper
 * @package application\helpers\enum_helper
 * @final
 */
abstract class E_DRAGGABLE extends BASE_Enum
{
	const YES	= true;
	const NO	= false;
}


/**
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_ERROR_VIEW extends BASE_Enum
{
	const GENERAL				= "errors/error_general";
	const INVALID_PARAMS		= "errors/error_invalid_parameter";
	const NOT_FOUND				= "errors/error_page_not_found";
	const NO_PERMISSION			= "errors/error_permission";
	const NO_SESSION			= "errors/error_session";
}

/**
 * @category helper
 * @package application\helpers\enum_helper
 * @final
 */
abstract class E_ENABLED extends BASE_Enum
{
	const YES	= true;
	const NO	= false;
}

/**
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 * @final
 */
abstract class E_ENVIRONMENT extends BASE_Enum
{
	const DEVELOPMENT 	= "development";
	const TESTING		= "testing";
	const PRODUCTION 	= "production";
}

/**
 * @category helper
 * @package application\helpers\enum_helper
 * @final
 */
abstract class E_FORMLAYOUT extends BASE_Enum
{
	const HORIZONTAL 	= "form-horizontal";
	const VERTICAL		= "form-vertical";
	const INLINE		= "form-inline";
}

/**
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 * @final
 */
abstract class E_FORMMETHOD extends BASE_Enum
{
	const POST	= "POST";
	const GET	= "GET";
}


/**
 * Used by the datatable
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @final
 */
abstract class E_DT_FILTER extends BASE_Enum
{
	const INPUT	= "input";
	const SELECT	= "select";
}

/**
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 * @final
 */
abstract class E_HORIZONTAL_POSITION extends BASE_Enum
{
	const LEFT	= 'left';
	const RIGHT	= 'right';
}

/**
 * Bootstrap validation states
 * control-label, form-control, and help-block will receive the validation styles.
 * 
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 * @final
 */
abstract class E_VALIDATION_STATES extends BASE_Enum
{
	const NONE			= "";
	const HAS_WARNING 	= "has-warning has-feedback";
	const HAS_ERROR		= "has-error has-feedback";
	const HAS_SUCCESS	= "has-success has-feedback";
}

/**
 * Font-Awesome (4.7) icons 
 * All font-awesome icons expect of the brand icons (add them if you miss them :-) )
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 4.7
 */
abstract class E_ICONS extends BASE_Enum
{
	/**
	 * Note: 	When you use E_ICONS::isValidValue() it will return false
	 * 
	 * @param int $size >> sizes 1-6 supported
	 * @param E_ICONS $icon
	 * @return string
	 */
	static function customSize($size, $icon){
		return str_replace("fa fa-", "fa fa-$size fa-", $icon);
	}
	
	const ADJUST						= '<i class="fa fa-adjust"></i>';
	const ADRESS_BOOK					= '<i class="fa fa-address-book"></i>';
	const ADRESS_BOOK_WHITE				= '<i class="fa fa-address-book-o"></i>';
	const ADRESS_CARD					= '<i class="fa fa-address-card"></i>';
	const ADRESS_CARD_WHITE				= '<i class="fa fa-address-card-o"></i>';
	const AMBULANCE						= '<i class="fa fa-ambulance"></i>';
	const AMERICAN_SIGN_LANGUAGE_I		= '<i class="fa fa-american-sign-language-interpreting"></i>';
	const ANGLE_DOUBLE_DOWN				= '<i class="fa fa-angle-double-down"></i>';
	const ANGLE_DOUBLE_LEFT				= '<i class="fa fa-angle-double-left"></i>';
	const ANGLE_DOUBLE_RIGHT			= '<i class="fa fa-angle-double-right"></i>';
	const ANGLE_DOUBLE_UP				= '<i class="fa fa-angle-double-up"></i>';
	const ANGLE_DOWN					= '<i class="fa fa-angle-down"></i>';
	const ANGLE_LEFT					= '<i class="fa fa-angle-left"></i>';
	const ANGLE_RIGHT					= '<i class="fa fa-angle-right"></i>';
	const ANGLE_UP						= '<i class="fa fa-angle-up"></i>';
	const ALIGN_CENTER					= '<i class="fa fa-align-center"></i>';
	const ALIGN_JUSTIFY					= '<i class="fa fa-align-justify"></i>';
	const ALIGN_LEFT					= '<i class="fa fa-align-left"></i>';
	const ALIGN_RIGHT					= '<i class="fa fa-align-right"></i>';
	const ANCHOR						= '<i class="fa fa-anchor"></i>';
	const ARCHIVE						= '<i class="fa fa-archive"></i>';
	const AREA_CHART					= '<i class="fa fa-area-chart"></i>';
	const ARROW_CIRCLE_DOWN_BLACK		= '<i class="fa fa-arrow-circle-down"></i>';
	const ARROW_CIRCLE_DOWN_WHITE		= '<i class="fa fa-arrow-circle-o-down"></i>';
	const ARROW_CIRCLE_LEFT_BLACK		= '<i class="fa fa-arrow-circle-left"></i>';
	const ARROW_CIRCLE_LEFT_WHITE		= '<i class="fa fa-arrow-circle-o-left"></i>';
	const ARROW_CIRCLE_RIGHT_BLACK		= '<i class="fa fa-arrow-circle-right"></i>';
	const ARROW_CIRCLE_RIGHT_WHITE		= '<i class="fa fa-arrow-circle-o-right"></i>';
	const ARROW_CIRCLE_UP_BLACK			= '<i class="fa fa-arrow-circle-up"></i>';
	const ARROW_CIRCLE_UP_WHITE			= '<i class="fa fa-arrow-circle-o-up"></i>';
	const ARROW_DOWN					= '<i class="fa fa-arrow-down"></i>';
	const ARROW_RIGHT					= '<i class="fa fa-arrow-left"></i>';
	const ARROW_LEFT					= '<i class="fa fa-arrow-right"></i>';
	const ARROW_UP						= '<i class="fa fa-arrow-up"></i>';
	const ARROWS						= '<i class="fa fa-arrows"></i>';
	const ARROWS_ALT					= '<i class="fa fa-arrows-alt"></i>';
	const ARROWS_H						= '<i class="fa fa-arrows-h"></i>';
	const ARROWS_V						= '<i class="fa fa-arrows-v"></i>';
	const ASSISTIVE_LISTENING_SYSTEM	= '<i class="fa fa-assistive-listening-systems"></i>';
	const ASTERISK						= '<i class="fa fa-asterisk"></i>';
	const AT							= '<i class="fa fa-at"></i>';
	const AUDIO_DESCRIPTION				= '<i class="fa fa-audio-description"></i>';
	const AUTOMOBILE					= '<i class="fa fa-automobile"></i>';
	
	const BACKWARD						= '<i class="fa fa-backward"></i>';
	const BALANCE_SCALE					= '<i class="fa fa-balance-scale"></i>';
	const BAN							= '<i class="fa fa-ban"></i>';
	const BANDCAMP						= '<i class="fa fa-bandcamp"></i>';
	const BANK							= '<i class="fa fa-bank"></i>';
	const BAR_CHART						= '<i class="fa fa-bar-chart"></i>';
	const BAR_CHART_O					= '<i class="fa fa-bar-chart-o"></i>';	// alias
	const BARCODE						= '<i class="fa fa-barcode"></i>';
	const BARS							= '<i class="fa fa-bars"></i>';
	const BATTERY_0						= '<i class="fa fa-battery-0"></i>';	// alias
	const BATTERY_1						= '<i class="fa fa-battery-1"></i>';	// alias
	const BATTERY_2						= '<i class="fa fa-battery-2"></i>';	// alias
	const BATTERY_3						= '<i class="fa fa-battery-3"></i>';	// alias
	const BATTERY_4						= '<i class="fa fa-battery-4"></i>';	// alias
	const BATH							= '<i class="fa fa-bath"></i>';
	const BED							= '<i class="fa fa-bed"></i>';
	const BEER							= '<i class="fa fa-beer"></i>';
	const BELL_BLACK					= '<i class="fa fa-bell"></i>';
	const BELL_WHITE					= '<i class="fa fa-bell-o"></i>';
	const BELL_SLASH					= '<i class="fa fa-bell-slash"></i>';
	const BELL_SLASH_WHITE				= '<i class="fa fa-bell-slash-o"></i>';
	const BICYCLE						= '<i class="fa fa-bicycle"></i>';
	const BINOCULARS					= '<i class="fa fa-binoculars"></i>';
	const BIRTHDAY_CAKE					= '<i class="fa fa-birthday-cake"></i>';
	const BITCOIN						= '<i class="fa fa-bitcoin"></i>';
	const BLIND							= '<i class="fa fa-blind"></i>';
	const BLUETOOTH						= '<i class="fa fa-bluetooth"></i>';
	const BLUETOOTH_B					= '<i class="fa fa-bluetooth-b"></i>';
	const BOLD							= '<i class="fa fa-bold"></i>';
	const BOLT							= '<i class="fa fa-bolt"></i>';
	const BOMB							= '<i class="fa fa-bomb"></i>';
	const BOOK							= '<i class="fa fa-book"></i>';
	const BOOKMARK_BLACK				= '<i class="fa fa-bookmark"></i>';
	const BOOKMARK_WHITE				= '<i class="fa fa-bookmark-o"></i>';
	const BRAILLE						= '<i class="fa fa-braille"></i>';
	const BRIEFCASE						= '<i class="fa fa-briefcase"></i>';
	const BUG							= '<i class="fa fa-bug"></i>';
	const BUILDING_BLACK				= '<i class="fa fa-building"></i>';
	const BUILDING_WHITE				= '<i class="fa fa-building-o"></i>';
	const BULLHORN						= '<i class="fa fa-bullhorn"></i>';
	const BULLSEYE						= '<i class="fa fa-bullseye"></i>';
	const BUS							= '<i class="fa fa-bus"></i>';
	
	const CAB							= '<i class="fa fa-cab"></i>';
	const CALCULATOR					= '<i class="fa fa-calculator"></i>';
	const CALENDAR						= '<i class="fa fa-calendar"></i>';
	const CALENDAR_CHECK_O				= '<i class="fa fa-calendar-check-o"></i>';
	const CALENDAR_MINUS_O				= '<i class="fa fa-calendar-minus-o"></i>';
	const CALENDAR_O					= '<i class="fa fa-calendar-o"></i>';
	const CALENDAR_PLUS_O				= '<i class="fa fa-calendar-plus-o"></i>';
	const CALENDAR_TIMES_O				= '<i class="fa fa-calendar-times-o"></i>';
	const CAMERA						= '<i class="fa fa-camera"></i>';
	const CAMERA_RETRO					= '<i class="fa fa-camera-retro"></i>';
	const CAR							= '<i class="fa fa-car"></i>';
	const CARET_DOWN					= '<i class="fa fa-caret-down"></i>';
	const CARET_LEFT					= '<i class="fa fa-caret-left"></i>';
	const CARET_RIGHT					= '<i class="fa fa-caret-right"></i>';
	const CARET_UP						= '<i class="fa fa-caret-up"></i>';
	const CARET_SQUARE_O_DOWN			= '<i class="fa fa-caret-square-o-down"></i>';
	const CARET_SQUARE_O_LEFT			= '<i class="fa fa-caret-square-o-left"></i>';
	const CARET_SQUARE_O_RIGHT			= '<i class="fa fa-caret-square-o-right"></i>';
	const CARET_SQUARE_O_UP				= '<i class="fa fa-caret-square-o-up"></i>';
	const CART_ARROW_DOWN				= '<i class="fa fa-cart-arrow-down"></i>';
	const CART_PLUS						= '<i class="fa fa-cart-plus"></i>';
	const CC							= '<i class="fa fa-cc"></i>';
	const CC_AMEX						= '<i class="fa fa-cc-amex"></i>';
	const CC_DINERS_CLUB				= '<i class="fa fa-cc-diners-club"></i>';
	const CC_DISCOVER					= '<i class="fa fa-cc-discover"></i>';
	const CC_JCB						= '<i class="fa fa-cc-jcb"></i>';
	const CC_MASTERCARD					= '<i class="fa fa-cc-mastercard"></i>';
	const CC_PAYPAL						= '<i class="fa fa-cc-paypal"></i>';
	const CC_STRIPE						= '<i class="fa fa-cc-stripe"></i>';
	const CC_VISA						= '<i class="fa fa-cc-visa"></i>';
	const CERTIFICATE					= '<i class="fa fa-certificate"></i>';
	const CHAIN							= '<i class="fa fa-chain"></i>';
	const CHAIN_BROKEN					= '<i class="fa fa-chain-broken"></i>';
	const CHECK							= '<i class="fa fa-check"></i>';
	const CHECK_CIRCLE_BLACK			= '<i class="fa fa-check-circle"></i>';
	const CHECK_CIRCLE_WHITE			= '<i class="fa fa-check-circle-o"></i>';
	const CHECK_SQUARE_BLACK			= '<i class="fa fa-check-square"></i>';
	const CHECK_SQUARE_WHITE			= '<i class="fa fa-check-square-o"></i>';
	const CHILD							= '<i class="fa fa-child"></i>';
	const CHEVRON_CIRCLE_DOWN			= '<i class="fa fa-chevron-circle-down"></i>';
	const CHEVRON_CIRCLE_LEFT			= '<i class="fa fa-chevron-circle-left"></i>';
	const CHEVRON_CIRCLE_RIGHT			= '<i class="fa fa-chevron-circle-right"></i>';
	const CHEVRON_CIRCLE_UP				= '<i class="fa fa-chevron-circle-up"></i>';
	const CHEVRON_DOWN					= '<i class="fa fa-chevron-down"></i>';
	const CHEVRON_LEFT					= '<i class="fa fa-chevron-left"></i>';
	const CHEVRON_RIGHT					= '<i class="fa fa-chevron-right"></i>';
	const CHEVRON_UP					= '<i class="fa fa-chevron-up"></i>';
	const CIRCLE_BLACK					= '<i class="fa fa-circle"></i>';
	const CIRCLE_WHITE					= '<i class="fa fa-circle-o"></i>';
	const CIRCLE_WHITE_NOTCH			= '<i class="fa fa-circle-o-notch"></i>';
	const CIRCLE_WHITE_NOTCH_SPIN		= '<i class="fa fa-circle-o-notch fa-spin"></i>';
	const CIRCLE_WHITE_THIN				= '<i class="fa fa-circle-thin"></i>';
	const CLIPBOARD						= '<i class="fa fa-clipboard"></i>';
	const CLOCK_WHITE					= '<i class="fa fa-clock-o"></i>';
	const CLONE_WHITE					= '<i class="fa fa-clone"></i>';
	const CLOSE							= '<i class="fa fa-close"></i>'; // alias
	const CLOUD							= '<i class="fa fa-cloud"></i>';
	const CLOUD_DOWNLOAD				= '<i class="fa fa-cloud-download"></i>';
	const CLOUD_UPLOAD					= '<i class="fa fa-cloud-upload"></i>';
	const CODE							= '<i class="fa fa-code"></i>';
	const CODE_FORK						= '<i class="fa fa-code-fork"></i>';
	const COFFEE						= '<i class="fa fa-coffee"></i>';
	const COG							= '<i class="fa fa-cog"></i>';
	const COG_SPIN						= '<i class="fa fa-cog fa-spin"></i>';
	const COGS							= '<i class="fa fa-cogs"></i>';
	const COLUMNS						= '<i class="fa fa-columns"></i>';
	const COPY							= '<i class="fa fa-copy"></i>';
	const COMMENT_BLACK					= '<i class="fa fa-comment"></i>';
	const COMMENT_WHITE					= '<i class="fa fa-comment-o"></i>';
	const COMMENTING_BLACK				= '<i class="fa fa-commenting"></i>';
	const COMMENTING_WHITE				= '<i class="fa fa-commenting-o"></i>';
	const COMMENTS_BLACK				= '<i class="fa fa-comments"></i>';
	const COMMENTS_WHITE				= '<i class="fa fa-comments-o"></i>';
	const COMPASS						= '<i class="fa fa-compass"></i>';
	const COMPRESS						= '<i class="fa fa-compress"></i>';
	const COPYRIGHT						= '<i class="fa fa-copyright"></i>';
	const CREATIVE_COMMONS				= '<i class="fa fa-creative-commons"></i>';
	const CREDIT_CARD_BLACK				= '<i class="fa fa-credit-card-alt"></i>';
	const CREDIT_CARD_WHITE				= '<i class="fa fa-credit-card"></i>';
	const CROP							= '<i class="fa fa-crop"></i>';
	const CROSSHAIRS					= '<i class="fa fa-crosshairs"></i>';
	const CUBE							= '<i class="fa fa-cube"></i>';
	const CUBES							= '<i class="fa fa-cubes"></i>';
	const CUT							= '<i class="fa fa-cut"></i>';
	const CUTLERY						= '<i class="fa fa-cutlery"></i>';
	
	const DASHBOARD						= '<i class="fa fa-dashboard"></i>';
	const DATABASE						= '<i class="fa fa-database"></i>';
	const DEAF							= '<i class="fa fa-deaf"></i>';
	const DEAFNESS						= '<i class="fa fa-deaf"></i>';	// alias
	const DEDENT						= '<i class="fa fa-dedent"></i>';	// alias
	const DESKTOP						= '<i class="fa fa-desktop"></i>';
	const DIAMOND						= '<i class="fa fa-diamond"></i>';
	const DOLLAR						= '<i class="fa fa-dollar"></i>';
	const DOT_CIRCLE_WHITE				= '<i class="fa fa-dot-circle-o"></i>';
	const DOWNLOAD						= '<i class="fa fa-download"></i>';
	const DRIVERS_LICENCE				= '<i class="fa fa-drivers-licence"></i>';
	const DRIVERS_LICENCE_WHITE			= '<i class="fa fa-drivers-licence-o"></i>';
	
	const EERCAST						= '<i class="fa fa-eercast"></i>';
	const EDIT							= '<i class="fa fa-edit"></i>';
	const EJECT							= '<i class="fa fa-eject"></i>';
	const ELLIPSIS_H					= '<i class="fa fa-ellipsis-h"></i>';
	const ELLIPSIS_V					= '<i class="fa fa-ellipsis-v"></i>';
	const ENVELOPE_BLACK				= '<i class="fa fa-envelope"></i>';
	const ENVELOPE_WHITE				= '<i class="fa fa-envelope-o"></i>';
	const ENVELOPE_SQUARE				= '<i class="fa fa-envelope-square"></i>';
	const ENVELOPE_OPEN					= '<i class="fa fa-envelope-open"></i>';
	const ENVELOPE_OPEN_WHITE			= '<i class="fa fa-envelope-open-o"></i>';
	const ERASER						= '<i class="fa fa-eraser"></i>';
	const ETSI							= '<i class="fa fa-etsy"></i>';
	const EURO							= '<i class="fa fa-euro"></i>';
	const EXCHANGE						= '<i class="fa fa-exchange"></i>';
	const EXCLAMATION					= '<i class="fa fa-exclamation"></i>';
	const EXCLAMATION_CIRCLE			= '<i class="fa fa-exclamation-circle"></i>';
	const EXCLAMATION_TRIANGLE			= '<i class="fa fa-exclamation-triangle"></i>';
	const EXPAND						= '<i class="fa fa-expand"></i>';
	const EXTERNAL_LINK					= '<i class="fa fa-external-link"></i>';
	const EXTERNAL_LINK_SQUARE			= '<i class="fa fa-external-link-square"></i>';
	const EYE							= '<i class="fa fa-eye"></i>';
	const EYE_SLASH						= '<i class="fa fa-eye-slash"></i>';
	const EYEDROPPER					= '<i class="fa fa-eyedropper"></i>';
	
	const FAST_BACKWARD					= '<i class="fa fa-fast-backward"></i>';
	const FAST_FORWARD					= '<i class="fa fa-fast-forward"></i>';
	const FAX							= '<i class="fa fa-fax"></i>';
	const FEED							= '<i class="fa fa-feed"></i>';
	const FEMALE						= '<i class="fa fa-female"></i>';
	const FIGHTER_JET					= '<i class="fa fa-fighter-jet"></i>';
	const FILE_BLACK					= '<i class="fa fa-file"></i>';
	const FILE_WHITE					= '<i class="fa fa-file-o"></i>';
	const FILE_ARCHIVE					= '<i class="fa fa-file-archive-o"></i>';
	const FILE_AUDIO					= '<i class="fa fa-file-audio-o"></i>';
	const FILE_CODE						= '<i class="fa fa-file-code-o"></i>';
	const FILE_EXCEL					= '<i class="fa fa-file-excel-o"></i>';
	const FILE_IMAGE					= '<i class="fa fa-file-image-o"></i>';
	const FILE_MOVIE					= '<i class="fa fa-file-video-o"></i>';
	const FILE_PDF						= '<i class="fa fa-file-pdf-o"></i>';
	const FILE_PHOTO					= '<i class="fa fa-file-photo-o"></i>';
	const FILE_POWERPOINT				= '<i class="fa fa-file-powerpoint-o"></i>';
	const FILE_SOUND					= '<i class="fa fa-file-sound-o"></i>';
	const FILE_TEXT_BLACK				= '<i class="fa fa-file-text"></i>';
	const FILE_TEXT_WHITE				= '<i class="fa fa-file-text-o"></i>';
	const FILE_VIDEO					= '<i class="fa fa-file-video-o"></i>';
	const FILE_WORD						= '<i class="fa fa-file-word-o"></i>';
	const FILE_ZIP						= '<i class="fa fa-file-zip-o"></i>';
	const FILES_WHITE					= '<i class="fa fa-files-o"></i>';
	const FILM							= '<i class="fa fa-film"></i>';
	const FILTER						= '<i class="fa fa-filter"></i>';
	const FIRE							= '<i class="fa fa-fire"></i>';
	const FLAG							= '<i class="fa fa-flag"></i>';
	const FLAG_CHECKERED				= '<i class="fa fa-flag-checkered"></i>';
	const FLAG_WHITE					= '<i class="fa fa-flag-o"></i>';
	const FLASH							= '<i class="fa fa-flash"></i>';
	const FLOPPY_WHITE					= '<i class="fa fa-floppy-o"></i>';
	const FLASK							= '<i class="fa fa-flask"></i>';
	const FOLDER_BLACK					= '<i class="fa fa-folder"></i>';
	const FOLDER_WHITE					= '<i class="fa fa-folder-o"></i>';
	const FOLDER_OPEN_BLACK				= '<i class="fa fa-folder-open"></i>';
	const FOLDER_OPEN_WHITE				= '<i class="fa fa-folder-open-o"></i>';
	const FONT							= '<i class="fa fa-font"></i>';
	const FREE_CODE_CAMP				= '<i class="fa fa-free-code-camp"></i>';
	const FROWN_WHITE					= '<i class="fa fa-frown-o"></i>';
	const FUTBOL_WHITE					= '<i class="fa fa-futbol-o"></i>';
	
	const GAMEPAD						= '<i class="fa fa-gamepad"></i>';
	const GAVEL							= '<i class="fa fa-gavel"></i>';
	const GEAR							= '<i class="fa fa-gear"></i>';	// alias
	const GEARS							= '<i class="fa fa-gears"></i>'; // alias
	const GIFT							= '<i class="fa fa-gift"></i>';
	const GLASS							= '<i class="fa fa-glass"></i>';
	const GLOBE							= '<i class="fa fa-globe"></i>';
	const GOOGLE_WALLET					= '<i class="fa fa-google-wallet"></i>';
	const GRADUATION_CAP				= '<i class="fa fa-graduation-cap"></i>';
	const GRAV							= '<i class="fa fa-grav"></i>';
	const GROUP							= '<i class="fa fa-group"></i>';

	const HAND_DOWN						= '<i class="fa fa-hand-o-down"></i>';
	const HAND_GRAB_WHITE				= '<i class="fa fa-hand-grab-o"></i>';
	const HAND_LEFT						= '<i class="fa fa-hand-o-left"></i>';
	const HAND_LIZARD_WHITE				= '<i class="fa fa-hand-lizard-o"></i>';
	const HAND_PAPER_WHITE				= '<i class="fa fa-hand-paper-o"></i>';
	const HAND_PEACE_WHITE				= '<i class="fa fa-hand-peace-o"></i>';
	const HAND_POINTER_WHITE			= '<i class="fa fa-hand-pointer-o"></i>';
	const HAND_RIGHT					= '<i class="fa fa-hand-o-right"></i>';
	const HAND_ROCK_WHITE				= '<i class="fa fa-hand-rock-o"></i>';
	const HAND_SCISSORS_WHITE			= '<i class="fa fa-hand-scissors-o"></i>';
	const HAND_SPOCK_WHITE				= '<i class="fa fa-hand-spock-o"></i>';
	const HAND_STOP_WHITE				= '<i class="fa fa-hand-stop-o"></i>';
	const HAND_UP						= '<i class="fa fa-hand-o-up"></i>';
	const HANDSHAKE						= '<i class="fa fa-handshake-o"></i>';
	const HARD_OF_HEARING				= '<i class="fa fa-hard-of-hearing"></i>';
	const HASHTAG						= '<i class="fa fa-hashtag"></i>';
	const HDD_WHITE						= '<i class="fa fa-hdd-o"></i>';
	const HEADER						= '<i class="fa fa-header"></i>';
	const HEADPHONES					= '<i class="fa fa-headphones"></i>';
	const HEART_BLACK					= '<i class="fa fa-heart"></i>';
	const HEART_WHITE 					= '<i class="fa fa-heart-o"></i>';
	const HEARTBEAT 					= '<i class="fa fa-heartbeat"></i>';
	const HYSTORY						= '<i class="fa fa-history"></i>';
	const HOME							= '<i class="fa fa-home"></i>';
	const HOSPITAL						= '<i class="fa fa-hospital"></i>';
	const HOTEL							= '<i class="fa fa-hotel"></i>';
	const HOURGLASS_BLACK				= '<i class="fa fa-hourglass"></i>';
	const HOURGLASS_WHITE				= '<i class="fa fa-hourglass-o"></i>';
	const HOURGLASS_1					= '<i class="fa fa-hourglass-1"></i>';
	const HOURGLASS_2					= '<i class="fa fa-hourglass-2"></i>';
	const HOURGLASS_3					= '<i class="fa fa-hourglass-3"></i>';
	
	const I_CURSOR						= '<i class="fa fa-i-cursor"></i>';
	const ID_BADGE						= '<i class="fa fa-id-badge"></i>';
	const ID_CARD						= '<i class="fa fa-id-card"></i>';
	const ID_CARD_WHITE					= '<i class="fa fa-id-card-o"></i>';
	const IMAGE							= '<i class="fa fa-image"></i>';
	const IMDB							= '<i class="fa fa-imdb"></i>';
	const INBOX							= '<i class="fa fa-inbox"></i>';
	const INDENT						= '<i class="fa fa-indent"></i>';
	const INDUSTRY						= '<i class="fa fa-industry"></i>';
	const INFO							= '<i class="fa fa-info"></i>';
	const INFO_CIRCLE					= '<i class="fa fa-info-circle"></i>';
	const INSTITUTION					= '<i class="fa fa-institution"></i>';
	const ITALIC						= '<i class="fa fa-italic"></i>';
	
	const KEY							= '<i class="fa fa-key"></i>';
	const KEYBOARD						= '<i class="fa fa-keyboard-o"></i>';
	
	const LANGUAGE						= '<i class="fa fa-language"></i>';
	const LAPTOP 						= '<i class="fa fa-laptop"></i>';
	const LEAF							= '<i class="fa fa-leaf"></i>';
	const LEGAL							= '<i class="fa fa-legal"></i>';
	const LEMON							= '<i class="fa fa-lemon-o"></i>';
	const LEVEL_DOWN					= '<i class="fa fa-level-down"></i>';
	const LEVEL_UP						= '<i class="fa fa-level-up"></i>';
	const LIFE_RING						= '<i class="fa fa-life-ring"></i>';
	const LIGHTBULB						= '<i class="fa fa-lightbulb-o"></i>';
	const LINE_CHART					= '<i class="fa fa-line-chart"></i>';
	const LINODE						= '<i class="fa fa-linode"></i>';
	const LINK 							= '<i class="fa fa-link"></i>';
	const LISTING						= '<i class="fa fa-list"></i>';
	const LISTING_ALT					= '<i class="fa fa-list-alt"></i>';
	const LISTING_OL					= '<i class="fa fa-list-ol"></i>';
	const LISTING_UL					= '<i class="fa fa-list-ul"></i>';
	const LOCATION_ARROW				= '<i class="fa fa-location-arrow"></i>';
	const LOCK							= '<i class="fa fa-lock"></i>';
	const LONG_ARROW_DOWN				= '<i class="fa fa-long-arrow-down"></i>';
	const LONG_ARROW_LEFT				= '<i class="fa fa-long-arrow-left"></i>';
	const LONG_ARROW_RIGHT				= '<i class="fa fa-long-arrow-right"></i>';
	const LONG_ARROW_UP					= '<i class="fa fa-long-arrow-up"></i>';
	const LOW_VISION					= '<i class="fa fa-low-vision"></i>';
	
	const MAGIC							= '<i class="fa fa-magic"></i>';
	const MAGNET						= '<i class="fa fa-magnet"></i>';
	const MAIL_FORWARD					= '<i class="fa fa-mail-forward"></i>';
	const MAIL_REPLY					= '<i class="fa fa-mail-reply"></i>';
	const MAIL_REPLY_ALL				= '<i class="fa fa-mail-reply-all"></i>';
	const MALE							= '<i class="fa fa-male"></i>';
	const MAP_BLACK						= '<i class="fa fa-map"></i>';
	const MAP_WHITE						= '<i class="fa fa-map-o"></i>';
	const MAP_MARKER					= '<i class="fa fa-map-marker"></i>';
	const MAP_PIN						= '<i class="fa fa-map-pin"></i>';
	const MAP_SIGNS						= '<i class="fa fa-map-signs"></i>';
	const MEDIKIT						= '<i class="fa fa-medikit"></i>';
	const MEETUP						= '<i class="fa fa-meetup"></i>';
	const MEH_WHITE						= '<i class="fa fa-meh-o"></i>';
	const MICROCHIP						= '<i class="fa fa-microchip"></i>';
	const MICROPHONE					= '<i class="fa fa-microphone"></i>';
	const MICROPHONE_SLASH				= '<i class="fa fa-microphone-slash"></i>';
	const MINUS							= '<i class="fa fa-minus"></i>';
	const MINUS_CIRCLE					= '<i class="fa fa-minus-circle"></i>';
	const MINUS_SQUARE_BLACK			= '<i class="fa fa-minus-square"></i>';
	const MINUS_SQUARE_WHITE			= '<i class="fa fa-minus-square-o"></i>';
	const MOBILE						= '<i class="fa fa-mobile"></i>';
	const MONEY							= '<i class="fa fa-money"></i>';
	const MOON_WHITE					= '<i class="fa fa-moon-o"></i>';
	const MORTAR_BOARD					= '<i class="fa fa-mortar-board"></i>';
	const MOTORCYCLE					= '<i class="fa fa-motorcycle"></i>';
	const MOUSE_POINTER					= '<i class="fa fa-mouse-pointer"></i>';
	const MUSIC							= '<i class="fa fa-music"></i>';
	
	const NAVICON						= '<i class="fa fa-navicon"></i>';
	const NEWSPAPER						= '<i class="fa fa-newspaper-o"></i>';
	
	const OBJECT_GROUP					= '<i class="fa fa-object-group"></i>';
	const OBJECT_UNGROUP				= '<i class="fa fa-object-ungroup"></i>';
	const OUTDENT						= '<i class="fa fa-outdent"></i>';
	
	const PAINT_BRUSH					= '<i class="fa fa-paint-brush"></i>';
	const PAPERCLIP						= '<i class="fa fa-paperclip"></i>';
	const PAPER_PLANE_BLACK				= '<i class="fa fa-paper-plane"></i>';
	const PAPER_PLANE_WHITE				= '<i class="fa fa-paper-plane-o"></i>';
	const PARAGRAPH						= '<i class="fa fa-paper-paragraph"></i>';
	const PASTE							= '<i class="fa fa-paste"></i>';
	const PAUSE							= '<i class="fa fa-pause"></i>';
	const PAUSE_CIRCLE_BLACK			= '<i class="fa fa-pause-circle"></i>';
	const PAUSE_CIRCLE_WHITE			= '<i class="fa fa-pause-circle-o"></i>';
	const PAW							= '<i class="fa fa-paw"></i>';
	const PAYPAL						= '<i class="fa fa-paypal"></i>';
	const PENCIL						= '<i class="fa fa-pencil"></i>';
	const PENCIL_SQUARE_BLACK			= '<i class="fa fa-pencil-square"></i>';
	const PENCIL_SQUARE_WHITE			= '<i class="fa fa-pencil-square-o"></i>';
	const PERCENT						= '<i class="fa fa-percent"></i>';
	const PHONE							= '<i class="fa fa-phone"></i>';
	const PHONE_SQUARE					= '<i class="fa fa-phone-square"></i>';
	const PHOTO							= '<i class="fa fa-photo"></i>';
	const PIE_CHART						= '<i class="fa fa-pie-chart"></i>';
	const PLANE							= '<i class="fa fa-plane"></i>';
	const PLAY							= '<i class="fa fa-play"></i>';
	const PLAY_CIRCLE_BLACK				= '<i class="fa fa-play-circle"></i>';
	const PLAY_CIRCLE_WHITE				= '<i class="fa fa-play-circle-o"></i>';
	const PLUG							= '<i class="fa fa-plug"></i>';
	const PLUS							= '<i class="fa fa-plus"></i>';
	const PLUS_CIRCLE					= '<i class="fa fa-plus-circle"></i>';
	const PLUS_SQUARE_BLACK				= '<i class="fa fa-plus-square"></i>';
	const PLUS_SQUARE_WHITE				= '<i class="fa fa-plus-square-o"></i>';
	const PODCAST						= '<i class="fa fa-podcast"></i>';
	const POWER_OFF						= '<i class="fa fa-power-off"></i>';
	const PRINTER						= '<i class="fa fa-print"></i>';
	const PUZZLE_PIECE					= '<i class="fa fa-puzzle-piece"></i>';
	
	const QRCODE						= '<i class="fa fa-qrcode"></i>';
	const QUESTION						= '<i class="fa fa-question"></i>';
	const QUESTION_CIRCLE_BLACK			= '<i class="fa fa-question-circle"></i>';
	const QUESTION_CIRCLE_WHITE			= '<i class="fa fa-question-circle-o"></i>';
	const QUOTE_LEFT					= '<i class="fa fa-quote-left"></i>';
	const QUOTE_RIGHT					= '<i class="fa fa-quote-right"></i>';
	const QUORA							= '<i class="fa fa-quora"></i>';
	
	const RANDOM						= '<i class="fa fa-random"></i>';
	const RAVELRY						= '<i class="fa fa-ravelry"></i>';
	const RECYCLE						= '<i class="fa fa-recycle"></i>';
	const REFRESH						= '<i class="fa fa-refresh"></i>';
	const REFRESH_SPIN					= '<i class="fa fa-refresh fa-spin"></i>';
	const REGISTERED					= '<i class="fa fa-registered"></i>';
	const REMOVE						= '<i class="fa fa-remove"></i>';
	const REORDER						= '<i class="fa fa-reorder"></i>';
	const REPEAT						= '<i class="fa fa-repeat"></i>';
	const REPLY							= '<i class="fa fa-reply"></i>';
	const REPLY_ALL						= '<i class="fa fa-reply-all"></i>';
	const RETWEET						= '<i class="fa fa-retweet"></i>';
	const ROAD							= '<i class="fa fa-road"></i>';
	const ROCKET						= '<i class="fa fa-rocket"></i>';
	const ROTATE_LEFT					= '<i class="fa fa-rotate-left"></i>';
	const ROTATE_RIGHT					= '<i class="fa fa-rotate-right"></i>';
	const RSS							= '<i class="fa fa-rss"></i>';
	const RSS_SQUARE					= '<i class="fa fa-rss-square"></i>';
	
	const SAVE							= '<i class="fa fa-save"></i>';
	const SCISSORS						= '<i class="fa fa-scissors"></i>';
	const SEARCH						= '<i class="fa fa-search"></i>';
	const SEARCH_MINUS					= '<i class="fa fa-search-minus"></i>';
	const SEARCH_PLUS					= '<i class="fa fa-search-plus"></i>';
	const SEND_BLACK					= '<i class="fa fa-send"></i>';
	const SEND_WHITE					= '<i class="fa fa-send-o"></i>';
	const SERVER						= '<i class="fa fa-server"></i>';
	const SHARE							= '<i class="fa fa-share"></i>';
	const SHARE_ALT						= '<i class="fa fa-share-alt"></i>';
	const SHARE_ALT_SQUARE				= '<i class="fa fa-share-alt-square"></i>';
	const SHARE_SQUARE_BLACK			= '<i class="fa fa-share-square"></i>';
	const SHARE_SQUARE_WHITE			= '<i class="fa fa-share-square-o"></i>';
	const SHIELD						= '<i class="fa fa-shield"></i>';
	const SHIP							= '<i class="fa fa-ship"></i>';
	const SHOPPING_BAG					= '<i class="fa fa-shopping-bag"></i>';
	const SHOPPING_BASKET				= '<i class="fa fa-shopping-basket"></i>';
	const SHOPPING_CART					= '<i class="fa fa-shopping-cart"></i>';
	const SHOWER						= '<i class="fa fa-shower"></i>';
	const SIGN_IN						= '<i class="fa fa-sign-in"></i>';
	const SIGN_LANGUAGE					= '<i class="fa fa-sign-language"></i>';
	const SIGN_OUT						= '<i class="fa fa-sign-out"></i>';
	const SIGNAL						= '<i class="fa fa-signal"></i>';
	const SITEMAP						= '<i class="fa fa-sitemap"></i>';
	const SLIDERS						= '<i class="fa fa-sliders"></i>';
	const SMILE							= '<i class="fa fa-smile-o"></i>';
	const SNOWFLAKE						= '<i class="fa fa-snowflake-o"></i>';
	const SOCCER_BALL					= '<i class="fa fa-soccer-ball-o"></i>';
	const SORT							= '<i class="fa fa-sort"></i>';
	const SORT_ALPHA_ASC				= '<i class="fa fa-sort-alpha-asc"></i>';
	const SORT_ALPHA_DESC				= '<i class="fa fa-sort-alpha-desc"></i>';
	const SORT_AMOUNT_ASC				= '<i class="fa fa-sort-amount-asc"></i>';
	const SORT_AMOUNT_DESC				= '<i class="fa fa-sort-amount-desc"></i>';
	const SORT_ASC						= '<i class="fa fa-sort-asc"></i>';
	const SORT_DESC						= '<i class="fa fa-sort-desc"></i>';
	const SORT_NUMERIC_ASC				= '<i class="fa fa-sort-numeric-asc"></i>';
	const SORT_NUMERIC_DESC				= '<i class="fa fa-sort-numeric-desc"></i>';
	const SPACE_SHUTTLE					= '<i class="fa fa-space-shuttle"></i>';
	const SPINNER						= '<i class="fa fa-spinner"></i>';
	const SPINNER_SPIN					= '<i class="fa fa-spinner fa-spin"></i>';
	const SPOON							= '<i class="fa fa-spoon"></i>';
	const SQUARE_BLACK					= '<i class="fa fa-square"></i>';
	const SQUARE_WHITE					= '<i class="fa fa-square-o"></i>';
	const STAR_BLACK					= '<i class="fa fa-star"></i>';
	const STAR_HALF_BLACK				= '<i class="fa fa-star-half"></i>';
	const STAR_WHITE					= '<i class="fa fa-star-o"></i>';
	const STAR_HALF_WHITE				= '<i class="fa fa-star-half-o"></i>';
	const STETHOSCOPE					= '<i class="fa fa-stethoscope"></i>';
	const STEP_BACKWARD					= '<i class="fa fa-step-backward"></i>';
	const STEP_FORWARD					= '<i class="fa fa-step-forward"></i>';
	const STICKY_NOTE_BLACK				= '<i class="fa fa-sticky-note"></i>';
	const STICKY_NOTE_WHITE				= '<i class="fa fa-sticky-note-o"></i>';
	const STREET_VIEW					= '<i class="fa fa-street-view"></i>';
	const STRIKETHROUGH					= '<i class="fa fa-strikethrough"></i>';
	const STOP							= '<i class="fa fa-stop"></i>';
	const STOP_CIRCLE_BLACK				= '<i class="fa fa-stop-circle"></i>';
	const STOP_CIRCLE_WHITE				= '<i class="fa fa-stop-circle-o"></i>';
	const SUBSCRIPT						= '<i class="fa fa-subscript"></i>';
	const SUITCASE						= '<i class="fa fa-suitcase"></i>';
	const SUN							= '<i class="fa fa-sun-o"></i>';
	const SUPERPOWERS					= '<i class="fa fa-superpowers"></i>';
	const SUPERSCRIPT					= '<i class="fa fa-superscript"></i>';
	const SUPPORT						= '<i class="fa fa-support"></i>';
	
	const TABLE							= '<i class="fa fa-table"></i>';
	const TABLET						= '<i class="fa fa-tablet"></i>';
	const TACHOMETER					= '<i class="fa fa-tachometer"></i>';
	const TAG							= '<i class="fa fa-tag"></i>';
	const TAGS							= '<i class="fa fa-tags"></i>';
	const TASKS							= '<i class="fa fa-tasks"></i>';
	const TAXI							= '<i class="fa fa-taxi"></i>';
	const TELEGRAM						= '<i class="fa fa-telegram"></i>';
	const TELEVISION					= '<i class="fa fa-television"></i>';
	const TEXT_HEIGHT					= '<i class="fa fa-text-height"></i>';
	const TEXT_WIDTH					= '<i class="fa fa-text-width"></i>';
	const TERMINAL						= '<i class="fa fa-terminal"></i>';
	const TH							= '<i class="fa fa-th"></i>';
	const TH_LARGE						= '<i class="fa fa-th-large"></i>';
	const TH_LIST						= '<i class="fa fa-th-list"></i>';
	const THERMOMETER					= '<i class="fa fa-thermometer"></i>';
	const THERMOMETER_EMPTY				= '<i class="fa fa-thermometer-empty"></i>';
	const THERMOMETER_FULL				= '<i class="fa fa-thermometer-full"></i>';
	const THERMOMETER_HALF				= '<i class="fa fa-thermometer-half"></i>';
	const THERMOMETER_QUARTER			= '<i class="fa fa-thermometer-quarter"></i>';
	const THERMOMETER_THREE_QUARTERS	= '<i class="fa fa-thermometer-three-quarters"></i>';
	const THUMB_TACK					= '<i class="fa fa-thumb-tack"></i>';
	const THUMBS_DOWN_BLACK				= '<i class="fa fa-thumbs-down"></i>';
	const THUMBS_DOWN_WHITE				= '<i class="fa fa-thumbs-o-down"></i>';
	const THUMBS_UP_BLACK				= '<i class="fa fa-thumbs-up"></i>';
	const THUMBS_UP_WHITE				= '<i class="fa fa-thumbs-o-up"></i>';
	const TICKET						= '<i class="fa fa-ticket"></i>';
	const TIMES							= '<i class="fa fa-times"></i>';
	const TIMES_RECTANGLE				= '<i class="fa fa-times-rectangle"></i>';
	const TIMES_RECTANGLE_WHITE			= '<i class="fa fa-times-rectangle-o"></i>';
	const TIMES_CIRCLE_BLACK			= '<i class="fa fa-times-circle"></i>';
	const TIMES_CIRCLE_WHITE			= '<i class="fa fa-times-circle-o"></i>';
	const TINT							= '<i class="fa fa-tint"></i>';
	const TOGGLE_DOWN					= '<i class="fa fa-toggle-down"></i>';
	const TOGGLE_LEFT					= '<i class="fa fa-toggle-left"></i>';
	const TOGGLE_OFF					= '<i class="fa fa-toggle-off"></i>';
	const TOGGLE_ON						= '<i class="fa fa-toggle-on"></i>';
	const TOGGLE_RIGHT					= '<i class="fa fa-toggle-right"></i>';
	const TOGGLE_UP						= '<i class="fa fa-toggle-up"></i>';
	const TRADEMARK						= '<i class="fa fa-trademark"></i>';
	const TRASH_BLACK					= '<i class="fa fa-trash"></i>';
	const TRASH_WHITE					= '<i class="fa fa-trash-o"></i>';
	const TREE							= '<i class="fa fa-tree"></i>';
	const TROPHY						= '<i class="fa fa-trophy"></i>';
	const TRUCK							= '<i class="fa fa-truck"></i>';
	const TTY							= '<i class="fa fa-tty"></i>';
	const TV							= '<i class="fa fa-tv"></i>';
	
	const UMBRELLA						= '<i class="fa fa-umbrella"></i>';
	const UNDERLINE						= '<i class="fa fa-underline"></i>';
	const UNDO							= '<i class="fa fa-undo"></i>';
	const UNIVERSAL_ACCESS				= '<i class="fa fa-universal-access"></i>';
	const UNIVERSITY					= '<i class="fa fa-university"></i>';
	const UNLINK						= '<i class="fa fa-unlink"></i>';
	const UNLOCK						= '<i class="fa fa-unlock"></i>';
	const UNLOCK_ALT					= '<i class="fa fa-unlock-alt"></i>';
	const UNSORTED						= '<i class="fa fa-unsorted"></i>';
	const UPLOAD						= '<i class="fa fa-upload"></i>';
	const USER							= '<i class="fa fa-user"></i>';
	const USER_WHITE					= '<i class="fa fa-user-o"></i>';
	const USER_CIRCLE					= '<i class="fa fa-user-circle"></i>';
	const USER_CIRCLE_WHITE				= '<i class="fa fa-user-circle-o"></i>';
	const USER_MEDICAL					= '<i class="fa fa-user-md"></i>';
	const USER_PLUS						= '<i class="fa fa-user-plus"></i>';
	const USER_SECRET					= '<i class="fa fa-user-secret"></i>';
	const USER_TIMES					= '<i class="fa fa-user-times"></i>';
	const USERS							= '<i class="fa fa-users"></i>';
	
	const VIDEO_CAMERA					= '<i class="fa fa-video-camera"></i>';
	const VOLUME_CONTROL_PHONE			= '<i class="fa fa-volume-control-phone"></i>';
	const VOLUME_DOWN					= '<i class="fa fa-volume-down"></i>';
	const VOLUME_OFF					= '<i class="fa fa-volume-off"></i>';
	const VOLUME_UP						= '<i class="fa fa-volume-up"></i>';
	
	const WARNING						= '<i class="fa fa-warning"></i>';
	const WHEELCHAIR					= '<i class="fa fa-wheelchair"></i>';
	const WHEELCHAIR_ALT				= '<i class="fa fa-wheelchair-alt"></i>';
	const WIFI							= '<i class="fa fa-wifi"></i>';
	const WINDOW_CLOSE					= '<i class="fa fa-window-close"></i>';
	const WINDOW_CLOSE_WHITE			= '<i class="fa fa-window-close-o"></i>';
	const WINDOW_MAXIMIZE				= '<i class="fa fa-window-maximize"></i>';
	const WINDOW_MINIMIZE				= '<i class="fa fa-window-minimize"></i>';
	const WINDOW_RESTORE				= '<i class="fa fa-window-restore"></i>';
	const WRENCH						= '<i class="fa fa-wrench"></i>';
	const WPEXPLORER					= '<i class="fa fa-wpexplorer"></i>';
	
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: ALIASES
	const ADD							= '<i class="fa fa-plus"></i>';
	const ATTACHMENT					= '<i class="fa fa-paperclip"></i>';
	const CANCEL						= '<i class="fa fa-times"></i>';
	const DANGER						= '<i class="fa fa-fire"></i>';
	const DELETE						= '<i class="fa fa-trash"></i>';
	const EDITOR 						= '<i class="fa fa-file"></i>';
	const EMAIL							= '<i class="fa fa-envelope"></i>';
	const OK							= '<i class="fa fa-check"></i>';
	
	const NO_ICON						= '';
	
	
	const CURRENY_BITCOIN				= '<i class="fa fa-bitcoin"></i>';
	const CURRENY_DOLLAR				= '<i class="fa fa-dollar"></i>';
	const CURRENY_EURO					= '<i class="fa fa-eur"></i>';
	const CURRENY_BRITISH_POUND			= '<i class="fa fa-gbp"></i>';
	const CURRENY_GG					= '<i class="fa fa-gg"></i>';
	const CURRENY_GG_CIRCLE				= '<i class="fa fa-gg-circle"></i>';
	const CURRENY_SHEKEL				= '<i class="fa fa-ils"></i>';
	const CURRENY_RUPEE					= '<i class="fa fa-inr"></i>';
	const CURRENY_ROUBLE				= '<i class="fa fa-rouble"></i>';
	const CURRENY_TURKISH_LIRA			= '<i class="fa fa-try"></i>';
	const CURRENY_WON					= '<i class="fa fa-won"></i>';
	const CURRENY_YEN					= '<i class="fa fa-jpy"></i>';
	
	const FORMITEM_REQUIRED				= '<i class="fa fa-asterisk small fa-1 required-correction-label" title="Pflichtfeld"></i>';
	
	const PANEL_CONTROL					= '<i class="fa fa-gear"></i>';
	const PANEL_DISMISS					= '<span class="glyphicon glyphicon-remove-sign"></span>';
	const PANEL_MINIMIZE				= '<span class="fa-chevron-down"></span>';
	
	const VEHICLE_AMBULANCE				= '<i class="fa fa-ambulance"></i>';
	const VEHICLE_BICYCLE				= '<i class="fa fa-bicycle"></i>';
	const VEHICLE_CAB					= '<i class="fa fa-cab"></i>';
	const VEHICLE_CAR					= '<i class="fa fa-car"></i>';
	const VEHICLE_BUS					= '<i class="fa fa-bus"></i>';
	const VEHICLE_MOTORCYCLE			= '<i class="fa fa-motorcycle"></i>';
	const VEHICLE_TRUCK					= '<i class="fa fa-truck"></i>';
	
	const ZOOM_IN						= '<i class="fa fa-search-plus"></i>';
	const ZOOM_OUT						= '<i class="fa fa-search-minus"></i>';

	const IS_STATIC						= '<i alt="ico-static" class="fa fa-shield"></i>';
}


/**
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_INPUTTYPE extends BASE_Enum
{
	const BUTTON			= "button";
	const DROPDOWN			= "dropdown";
	const FILE				= "file";
	const TEXT 				= "text";
	const TEXT_AREA			= "textarea";
	const PASSWORD 			= "password";
	const SUBMIT			= "submit";
	const TOGGLE			= "toggle";
	const CHECKBOX			= "checkbox";
	const RADIO				= "radio";
	const SELECT			= "select";
	const HIDDEN			= "hidden";
	const DATETIME			= "datetime";
	const DATETIME_LOCAL	= "datetime-local";
	const DATE				= "date";
	const MONTH				= "month";
	const TIME				= "time";
	const WEEK				= "week";
	const NUMBER			= "number";
	const EMAIL				= "email";
	const URL				= "url";
	const SEARCH			= "search";
	const TEL				= "tel";
	const COLOR				= "color";
}

/**
 * Supported localizations
 *
 * @author _BA5E
 * @category helper
 * @package application\helpers\enum_helper
 * @since 1.2
 * @version 1.0
 */
abstract class E_LANGUAGES extends BASE_Enum
{
	const DE = "DE";
	const EN = "EN";
	const IT = "IT";
	const FR = "FR";
}


/**
 * Bootstrap image classes
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_IMAGE_STYLES extends BASE_Enum
{
	const ROUNDED 	= "img-rounded";
	const CIRCLE	= "img-circle";
	const THUMBNAIL	= "img-thumbnail";
}

/**
 * @category helper
 * @package application\helpers\enum_helper
 * @final
 */
abstract class E_INLINE extends BASE_Enum
{
	const YES 	= true;
	const NO 	= false;
}

/**
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_LOGLEVEL extends BASE_Enum
{
	const ALL 		= "ALL";
	const INFO 		= "INFO";
	const CRITICAL	= "CRITICAL";
	const ERROR		= "ERROR";
	const DEBUG		= "DEBUG";
}

/**
 * Lorem Ipsum placeholder text as shortcut
 * 
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_LOREM extends BASE_Enum
{
	const IPSUM_STRING	= "Lorem ipsum dolor sit amet Consectetuer Phasellus pretium vel nibh Quis vel tellus a at Convallis sed ipsum nulla et Pellentesque Fusce Praesent Nunc Suspendisse. Nulla facilisi mauris elit at Id semper Nullam ac Ut At enim convallis consectetuer semper Dui Sed hendrerit dictumst congue Mauris fringilla turpis nulla Aliquam. At ligula velit Donec urna Cursus feugiat Vestibulum augue augue Tellus enim Phasellus lacus pellentesque Condimentum tellus ac sapien interdum. Lorem ipsum dolor sit amet Consectetuer Phasellus pretium vel nibh Quis vel tellus a at Convallis sed ipsum nulla et Pellentesque Fusce Praesent Nunc Suspendisse. Nulla facilisi mauris elit at Id semper Nullam ac Ut At enim convallis consectetuer semper Dui Sed hendrerit dictumst congue Mauris fringilla turpis nulla Aliquam. At ligula velit Donec urna Cursus feugiat Vestibulum augue augue Tellus enim Phasellus lacus pellentesque Condimentum tellus ac sapien interdum";
}

/**
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_PANEL_LAYOUT extends BASE_Enum
{
	const DEFAULT_HEADING 	= "TOP";
	const TOP_HEADING 		= "TOP";
	const LEFT_HEADING 		= "LEFT";
}

/**
 * Permission enumerations - Reflect the rights stored in the database
 * Extensions of the BASE_Enum class located in "application/core"
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_PERMISSIONS extends BASE_Enum
{
	// ..::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: ROOT PERMISSIONS ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	const ROOT_CRUD_BUILDER				= "root_right_crud_builder";
	
	// ..:: CLIENTS :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	const ROOT_CLIENT_LIST 				= "root_right_client_list";
	const ROOT_CLIENT_DELETE 			= "root_right_client_delete";
	const ROOT_CLIENT_CREATE			= "root_right_client_write";
	const ROOT_CLIENT_EDIT 				= "root_right_client_write";
	
	// ..:: LOCALES :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	const ROOT_LOCALE_LIST 				= "root_right_locale_list";
	const ROOT_LOCALE_CREATE			= "root_right_locale_write";
	const ROOT_LOCALE_EDIT  			= "root_right_locale_write";
	const ROOT_LOCALE_DELETE 			= "root_right_locale_delete";
	const ROOT_LOCALE_GENERATE 			= "root_right_locale_generate";
	
	// ..:: Virtual permission indicates that the user is member of root-users (has at least one root permission assigned)
	const IS_ROOT 						= "is_root";				
	
	// ..::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: REGULAR PERMISSIONS :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	
	// ..:: ROLES :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	const ROLE_LIST 					= "right_role_list";
	const ROLE_DELETE 					= "right_role_delete";
	const ROLE_CREATE 					= "right_role_write";
	const ROLE_EDIT 					= "right_role_write";
	
	// ..:: USERS :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	const USER_LIST 					= "right_user_list";
	const USER_DELETE 					= "right_user_delete";
	const USER_CREATE 					= "right_user_write";
	const USER_EDIT 					= "right_user_write";
	const USER_EXPORT					= "right_user_write";
}

/**
 * Combine all relevant css & js files for specific plugins
 * requires PHP > 5.6 (Since PHP 5.6, you can declare an array constant with 'const')
 * 
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_PLUGIN extends BASE_Enum
{
	const BS_DATETIMEPICKER	= array("css" => array(E_PLUGINS_CSS::BS_DATETIMEPICKER), "js"=> array(E_PLUGINS_JS::BS_DATETIMEPICKER),  "locales_dir"=>"bootstrap-datetimepicker/2.4.4/js/locales/");
	const BS_SLIDER			= array("css" => array(E_PLUGINS_CSS::BS_SLIDER), "js"=> array(E_PLUGINS_JS::BS_SLIDER));
	const BS_TOGGLE 		= array("css" => array(E_PLUGINS_CSS::BS_TOGGLE), "js"=> array(E_PLUGINS_JS::BS_TOGGLE) );
	const FILE_INPUT		= array("css" => array(E_PLUGINS_CSS::BS_FILEINPUT), "js"=> array(E_PLUGINS_JS::BS_FILEINPUT ));   //str_replace("{locale}", "de", E_PLUGINS_JS::BS_FILEINPUT_LOCALE )
	
	const DATATABLES 		= array(
		"css" => array(
				E_PLUGINS_CSS::BS_DATATABLES, E_PLUGINS_CSS::DATATABLES_RESPONSIVE_BS, E_PLUGINS_CSS::DATATABLES_BUTTONS, 
				E_PLUGINS_CSS::DATATABLES_BUTTONS_BS, E_PLUGINS_CSS::DATATABLES_FIXED_HEADER_BS, E_PLUGINS_CSS::DATATABLES_SELECT_BS,
				E_PLUGINS_CSS::DATATABLES_FIXED_COLUMNS_BS
		), 
		"js"=>array(
			E_PLUGINS_JS::DATATABLES, E_PLUGINS_JS::BS_DATATABLES, E_PLUGINS_JS::DATATABLES_RESPONSIVE, E_PLUGINS_JS::DATATABLES_RESPONSIVE_BS, 
			E_PLUGINS_JS::DATATABLES_BUTTONS, E_PLUGINS_JS::DATATABLES_BUTTONS_BS, E_PLUGINS_JS::DATATABLES_BUTTONS_HTML5, 
			E_PLUGINS_JS::DATATABLES_BUTTONS_COLVIS, E_PLUGINS_JS::DATATABLES_COL_REORDER, E_PLUGINS_JS::COL_RESIZEABLE, E_PLUGINS_JS::DATATABLES_FIXED_HEADER, E_PLUGINS_JS::DATATABLES_FIXED_COLUMNS,  
			E_PLUGINS_JS::JSZIP, E_PLUGINS_JS::PDF_MAKE, E_PLUGINS_JS::PDF_MAKE_VFS, E_PLUGINS_JS::DATATABLES_SELECT
		) 
	);
	
    const SELECT2           = array("css" => array(E_PLUGINS_CSS::SELECT2, E_PLUGINS_CSS::SELECT2_BS_THEME), "js"=> array(E_PLUGINS_JS::SELECT2_FULL), "locales_dir"=>"select2/4.0.3/js/i18n/" );

    const ICHECK	 		= array("css" => array(E_PLUGINS_CSS::ICHECK), "js"=>array(E_PLUGINS_JS::ICHECK));
	
	const TINY_MCE			= array("css" => array(), "js"=>array(E_PLUGINS_JS::TINY_MCE));
}

/**
 * Here we store the relative paths, to css files in the cdn directory
 * @see also config/config_custom.php >> $config['cdn'] for the URL
 *
 * The {min} placeholder will be replaced in the skeleton view depending on the ENVIRONMENT 
 * 
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_PLUGINS_CSS extends BASE_Enum
{
	const ANIMATE								= "animate/3.5.1/css/animate-3.5.1{min}.css";												// cool animations >> animate.css -http://daneden.me/animate
	
	const BS_CALENDAR							= "bootstrap-calendar/0.2.4/css/calendar-0.2.4{min}.css";									// calendar for bootstrap
	const BS_DATATABLES							= "datatables/1.10.12/css/dataTables.bootstrap-1.10.12{min}.css";							// bootstrap extension for the jQuery datatables
	const BS_DATETIMEPICKER						= "bootstrap-datetimepicker/2.4.4/css/bootstrap-datetimepicker-2.4.4{min}.css";				// date and time picker plugin (@link https://www.malot.fr/bootstrap-datetimepicker))
	const BS_FILEINPUT							= "bootstrap-fileinput/4.3.5/css/fileinput-4.3.5{min}.css";									// upload plugin (@link https://plugins.krajee.com/)
	const BS_TOGGLE								= "bootstrap-toggle/2.2.0/css/bootstrap-toggle-2.2.0{min}.css";								// plugin to convert checkboxes into toggles
	const BS_SLIDER								= "bootstrap-slider/10.2.0/css/bootstrap-slider-10.2.0{min}.css";							// slider plugin (@link https://github.com/seiyria/bootstrap-slider)
	const BS_SWITCH								= "bootstrap-switch/3.3.2/css/bootstrap-switch-3.3.2{min}.css";								// switch plugin
	
	const DATATABLES							= "datatables/1.10.12/css/jquery.dataTables-1.10.12{min}.css";								// the famous datatables
	const DATATABLES_BUTTONS					= "datatables/1.10.12/extensions/Buttons/css/buttons.dataTables{min}.css";					// buttons extensions for datatables
	const DATATABLES_AUTOFILL_BS				= "datatables/1.10.12/extensions/AutoFill/css/autoFill.bootstrap{min}.css";					// buttons extensions for datatables
	const DATATABLES_BUTTONS_BS					= "datatables/1.10.12/extensions/Buttons/css/buttons.bootstrap{min}.css";					// buttons extensions for datatables
	const DATATABLES_COL_REORDER_BS				= "datatables/1.10.12/extensions/ColReorder/css/colReorder.bootstrap{min}.css";				// column reorder extensions for datatables
	const DATATABLES_FIXED_COLUMNS_BS			= "datatables/1.10.12/extensions/FixedColumns/css/fixedColumns.bootstrap{min}.css";			// fixed columns extension extensions for datatables
	const DATATABLES_FIXED_HEADER_BS			= "datatables/1.10.12/extensions/FixedHeader/css/fixedHeader.bootstrap{min}.css";			// hixed header extensions for datatables
	const DATATABLES_KEYTABLE_BS				= "datatables/1.10.12/extensions/KeyTable/css/keyTable.bootstrap{min}.css";					// keyTable extensions for datatables
	const DATATABLES_RESPONSIVE_BS				= "datatables/1.10.12/extensions/Responsive/css/responsive.bootstrap{min}.css";				// responsive extensions for datatables
	const DATATABLES_ROWREORDER_BS				= "datatables/1.10.12/extensions/RowReorder/css/rowReorder.bootstrap{min}.css";				// RowReorder extensions for datatables
	const DATATABLES_SCROLLER_BS				= "datatables/1.10.12/extensions/Scroller/css/scroller.bootstrap{min}.css";					// Scroller extensions for datatables
	const DATATABLES_SELECT_BS					= "datatables/1.10.12/extensions/Select/css/select.bootstrap{min}.css";						// Select extensions for datatables
	
	const FILE_INPUT							= "font-awesome/4.7.0/css/font-awesome-4.7.0{min}.css";										// iconset
	const FONTAWESOME							= "font-awesome/4.7.0/css/font-awesome-4.7.0{min}.css";										// iconset
	
	const ICHECK								= "icheck/1.0.2/css/minimal/minimal.css";													// checkbox and radiobutton plugin
	
	const JQUERY_UI								= "jquery-ui/1.12.1-no-theme/css/jquery-ui-1.12.1{min}.css";								// jqueryUI
	const JQUERY_GROWL							= "jGrowl/1.4.5/css/jquery.jgrowl-1.4.5{min}.css";											// jGrowl is an unobtrusive notification system for web applications.

	const SELECT2								= "select2/4.0.3/css/select2-4.0.3{min}.css";												// another dropdown plugin
	const SELECT2_BS_THEME						= "select2/4.0.3/css/select2-bootstrap{min}.css";											// bootstrap theme for select2
}

/**
 * Here we store the relative paths, to js files in the cdn directory. All available js-plugins located at the content delivery network 
 * @see also config/config_custom.php >> $config['cdn'] for the URL
 *
 * The {min} placeholder will be replaced in the skeleton view depending on the ENVIRONMENT 
 * 
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_PLUGINS_JS extends BASE_Enum
{
	const AUTONUMERIC							= "autonumeric/1.9.46/js/autonumeric-1.9.46{min}.js";										// a jQuery plugin that automatically formats currency and numbers as you type on form inputs (@link http://decorplanit.com/plugin/). 
	
	const BOOTSTRAP	 							= "bootstrap/3.3.7/js/bootstrap-3.3.7{min}.js";												// requires jQuery
	const BS_CALENDAR							= "bootstrap-calendar/0.2.4/js/calendar-0.2.4{min}.js";										// calendar for bootstrap
	const BS_DATATABLES							= "datatables/1.10.12/js/dataTables.bootstrap-1.10.12{min}.js";								// the datatables for bootstrap !!! Also requires the regular jquery datatables !!!
	const BS_DATETIMEPICKER						= "bootstrap-datetimepicker/2.4.4/js/bootstrap-datetimepicker-2.4.4{min}.js";				// date and time picker plugin (@link https://www.malot.fr/bootstrap-datetimepicker)
	const BS_FILEINPUT							= "bootstrap-fileinput/4.3.5/js/fileinput-4.3.5{min}.js";									// upload plugin (@link https://plugins.krajee.com/)
	const BS_FILEINPUT_LOCALE					= "bootstrap-fileinput/4.3.5/js/locales/{locale}.js";										// fileinput localization
	const BS_TOGGLE								= "bootstrap-toggle/2.2.0/js/bootstrap-toggle-2.2.0{min}.js";								// plugin to convert checkboxes into toggles
	const BS_SLIDER								= "bootstrap-slider/10.2.0/js/bootstrap-slider-10.2.0{min}.js";								// slider plugin (@link https://github.com/seiyria/bootstrap-slider)
	const BS_SWITCH								= "bootstrap-switch/3.3.2/js/bootstrap-switch-3.3.2{min}.js";								// switch plugin
	const BS_VALIDATOR							= "bootstrap-validator/0.11.19/js/validator-0.11.9{min}.js";								// validation plugin (@link 1000hz.github.io/bootstrap-validator/#validator)

	const COL_RESIZEABLE						= "colResizable/1.6/js/colResizable-1.6{min}.js";											// column resize plugin (@link http://www.bacubacu.com/colresizable/)
	
	const DATATABLES							= "datatables/1.10.12/js/jquery.dataTables-1.10.12{min}.js";								// the datatables plugins
	const DATATABLES_AUTOFILL					= "datatables/1.10.12/extensions/AutoFill/js/dataTables.autoFill{min}.js";					// buttons extensions for datatables
	const DATATABLES_BUTTONS					= "datatables/1.10.12/extensions/Buttons/js/dataTables.buttons{min}.js";					// buttons extensions for datatables
	const DATATABLES_BUTTONS_HTML5				= "datatables/1.10.12/extensions/Buttons/js/buttons.html5{min}.js";							// HTML5 export buttons extensions for datatables
	const DATATABLES_BUTTONS_PRINT				= "datatables/1.10.12/extensions/Buttons/js/buttons.print{min}.js";							// Print button extensions for datatables
	const DATATABLES_BUTTONS_COLVIS				= "datatables/1.10.12/extensions/Buttons/js/buttons.colVis{min}.js";						// ColVis export buttons extensions for datatables
	const DATATABLES_COL_REORDER				= "datatables/1.10.12/extensions/ColReorder/js/dataTables.colReorder{min}.js";				// column reorder extensions for datatables
	const DATATABLES_COL_REORDER_AND_RESIZE		= "datatables/1.10.12/extensions/ColReorderWithResize/js/ColReorderWithResize_fork.js";		// FORK: column reorder extensions for datatables
	const DATATABLES_FIXED_COLUMNS				= "datatables/1.10.12/extensions/FixedColumns/js/dataTables.fixedColumns{min}.js";			// fixed columns extension extensions for datatables
	const DATATABLES_FIXED_HEADER				= "datatables/1.10.12/extensions/FixedHeader/js/dataTables.fixedHeader{min}.js";			// hixed header extensions for datatables
	const DATATABLES_KEYTABLE					= "datatables/1.10.12/extensions/KeyTable/js/dataTables.keyTable{min}.js";					// keyTable extensions for datatables
	const DATATABLES_RESPONSIVE					= "datatables/1.10.12/extensions/Responsive/js/dataTables.responsive{min}.js";				// responsive extensions for datatables
	const DATATABLES_ROWREORDER					= "datatables/1.10.12/extensions/RowReorder/js/dataTables.rowReorder{min}.js";				// RowReorder extensions for datatables
	const DATATABLES_SCROLLER					= "datatables/1.10.12/extensions/Scroller/js/dataTables.scroller{min}.js";					// Scroller extensions for datatables
	const DATATABLES_SELECT						= "datatables/1.10.12/extensions/Select/js/dataTables.select{min}.js";						// Select extensions for datatables
	const DATATABLES_AUTOFILL_BS				= "datatables/1.10.12/extensions/AutoFill/js/autoFill.bootstrap{min}.js";					// buttons extensions for datatables
	const DATATABLES_BUTTONS_BS					= "datatables/1.10.12/extensions/Buttons/js/buttons.bootstrap{min}.js";						// buttons extensions for datatables
	const DATATABLES_RESPONSIVE_BS				= "datatables/1.10.12/extensions/Responsive/js/responsive.bootstrap{min}.js";				// responsive extensions for datatables
	
	const FLOT									= "flot/0.8.3/js/jquery.flot.js";															// jquery chart plugin (also use excanvas for the frickin IE)
	const FLOT_CANVAS							= "flot/0.8.3/js/jquery.flot.canvas.js";										
	const FLOT_CATEGORIES						= "flot/0.8.3/js/jquery.flot.categories.js";									
	const FLOT_CROSSHAIR						= "flot/0.8.3/js/jquery.flot.crosshair.js";												
	const FLOT_ERRORBARS						= "flot/0.8.3/js/jquery.flot.errorbars.js";												
	const FLOT_FILLBETWEEN						= "flot/0.8.3/js/jquery.flot.fillbetween.js";												
	const FLOT_IMAGES							= "flot/0.8.3/js/jquery.flot.image.js";		
	const FLOT_NAVIGATE							= "flot/0.8.3/js/jquery.flot.navigate.js";
	const FLOT_PIE								= "flot/0.8.3/js/jquery.flot.pie.js";
	const FLOT_RESIZE							= "flot/0.8.3/js/jquery.flot.resize.js";
	const FLOT_SELECTION						= "flot/0.8.3/js/jquery.flot.selection.js";
	const FLOT_STACK							= "flot/0.8.3/js/jquery.flot.stack.js";
	const FLOT_SYMBOL							= "flot/0.8.3/js/jquery.flot.symbol.js";
	const FLOT_THRESHOLD						= "flot/0.8.3/js/jquery.flot.threshold.js";
	const FLOT_TIME								= "flot/0.8.3/js/jquery.flot.time.js";
	
	const ICHECK								= "icheck/1.0.2/js/icheck-1.0.2{min}.js";													// checkbox and radiobutton plugin
	
	const JQUERY								= "jquery/2.2.4/js/jquery-2.2.4{min}.js";													// Note that Bootstrap's (3.3.7) JavaScript requires jQuery version 1.9.1 or higher, but lower than version 4
	const JQUERY_BACKSTRETCH					= "jquery-backstretch/2.1.16/js/jquery.backstretch-2.1.16{min}.js";							// jQuery plugin that allows you to add a dynamically-resized, slideshow-capable background image to any page or element. 
	const JQUERY_BLOCKUI						= "jquery-blockUI/2.70.0/js/jquery.blockUI.js";												// jQuery blockUI plugin - Requires jQuery v1.7 or later
	const JQUERY_GROWL							= "jGrowl/1.4.5/js/jquery.jgrowl-1.4.5{min}.js";											// jGrowl is an unobtrusive notification system for web applications.
	const JQUERY_MASK_PLUGIN					= "jquery-mask-plugin/1.14.15/js/jquery.mask-1.14.15{min}.js";								// masked input plugin (@link https://igorescobar.github.io/jQuery-Mask-Plugin/)
	const JQUERY_NICESCROLL						= "jquery-nicescroll/3.6.8/js/jquery.nicescroll-3.6.8{min}.js";								// Scroller plugin
	const JQUERY_UI								= "jquery-ui/1.12.1-no-theme/js/jquery-ui-1.12.1{min}.js";									// the jqueryUI library
	const JQUERY_UI_TOUCHPUNCH					= "jquery-ui-touchpunch/0.2.3/js/jquery.ui.touch-punch-0.2.3{min}.js";						// jQuery UI Touch Punch is a small hack that enables the use of touch events on sites using the jQuery UI user interface library.

	const JSZIP									= "jszip/2.5.0/js/jszip-2.5.0{min}.js";														// https://stuk.github.io/jszip/
	
	const MOMENT_JS								= "moment-js/2.22.0/js/moment-2.22.0{min}.js";												// date and time functions
	const MOMENT_JS_WITH_LOCALES				= "moment-js/2.22.0/js/moment-with-locales-2.22.0{min}.js";									// date and time functions with locales
	const MOMENT_TIMEZONE						= "moment-timezone/0.5.5/js/moment-timezone-0.5.5{min}.js";									// date and time functions with timezones
	const MOMENT_TIMEZONE_WITH_DATA				= "moment-timezone/0.5.5/js/moment-timezone-with-data-0.5.5{min}.js";						// date and time functions with data
	const MOMENT_TIMEZONE_WITH_DATA_2010_2020	= "moment-timezone/0.5.5/js/moment-timezone-with-data-2010-2020-0.5.5{min}.js";				// date and time functions with data
	
	const PDF_MAKE								= "pdfmake/0.1.18/js/pdfmake-0.1.18{min}.js";												// Client/server side PDF printing in pure JavaScript
	const PDF_MAKE_VFS							= "pdfmake/0.1.18/js/vfs_fonts.js";															// default font definition
	
	const SELECT2								= "select2/4.0.3/js/select2-4.0.3{min}.js";													// select2 dropdown plugin
	const SELECT2_FULL							= "select2/4.0.3/js/select2-4.0.3.full{min}.js";											// select2 dropdown plugin
	const SLIMSCROLL							= "slimscroll/1.3.8/js/jquery.slimscroll-1.3.8{min}.js";									// scrollbar plugin
	
	const TINY_MCE								= "tinymce/4.5.3/js/tinymce-4.5.3.min.js";													// TinyMCE wysiwyg editor
}


/**
 * All available rendermodes 
 * 
 * @see BASE_Controller::render
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_RENDERMODE extends BASE_Enum
{
	const FULLPAGE		= "FULLPAGE";
	const AJAX 			= "AJAX";		
	const AJAX_PLAIN	= "AJAX_PLAIN";
	const JSON			= "JSON";
	const JSON_DATA		= "JSON_DATA";
	const NONE			= "NONE"; 
}

/**
 * @category helper
 * @package application\helpers\enum_helper
 * @final
 */
abstract class E_REQUIRED extends BASE_Enum
{
	const YES	= true;
	const NO	= false;
}

/**
 * List of keys which are reserved and cannot be used with the BASE_Controller's setViewData-Method
 * 
 * @see BASE_Controller::setViewData
 * 
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_RESEREVED_DATAKEYS extends BASE_Enum
{
	const BREADCRUMB		= "breadcrumb";
	const DEBUG_FILENAME 	= "DEBUG_FILENAME";
	const CALLER_CLASS 		= "caller_class";
	const CLIENT_ID			= "client_id";
	const JS_ENABLED		= "js_enabled";
}

/**
 * Bootstrap responsice classes
 * 
 * For faster mobile-friendly development, use these utility classes for showing and hiding content by device via media query. Also included are utility classes for toggling content when printed.
 * Try to use these on a limited basis and avoid creating entirely different versions of the same site. Instead, use them to complement each device's presentation.
 * 
 * Use a single or combination of the available classes for toggling content across viewport breakpoints.
 * 
 * @see bootstrap #responsive-utilities 
 * @link https://getbootstrap.com/docs/3.3/css/#responsive-utilities
 *  
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_RESPONSIVE_CLASSES extends BASE_Enum
{
	const VISIBLE_XS	= "visible-xs";
	const VISIBLE_SM 	= "visible-sm";	
	const VISIBLE_MD	= "visible-md";
	const VISIBLE_LG	= "visible-lg";
	
	const HIDDEN_XS		= "hidden-xs";
	const HIDDEN_SM 	= "hidden-sm";
	const HIDDEN_MD		= "hidden-md";
	const HIDDEN_LG		= "hidden-lg";
}

/**
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 * @final
 */
abstract class E_SELECTED extends BASE_Enum
{
	const YES	= true;
	const NO	= false;
}

/**
 * List of available Session-Items (array-keys)
 * 
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_SESSION_ITEM extends BASE_Enum
{
	
	const GOTO_AFTER_LOGIN		= "goto_after_login";
	const SESSION_ID			= "session_id";
	const IS_ROOT				= "is_root";
	const JS_ENABLED			= "js_enabled";
	const JS_LOCAL_STORAGE		= "local_storage";
	
	const SIDEBAR_EXPANDED		= "sidebar_expanded";
	const SIDEBAR_SELECTED_ITEM	= "sidebar_selected_item";
	const LOGGED_IN				= "logged_in";
	const LOGGED_IN_AT			= "logged_in_at";
	
	const CLIENT_ID				= "client_id";
	const CLIENT_NAME			= "client_name";
	const CLIENT_DESC			= "client_desc";
	const CLIENT_EMAIL			= "client_email";
	const CLIENT_PHONE			= "client_phone";
	const CLIENT_FAX			= "client_fax";
	const CLIENT_STREET			= "client_street";
	const CLIENT_HOUSE_NR		= "client_house_nr";
	const CLIENT_ZIPCODE		= "client_zipcode";
	const CLIENT_LOCATION		= "client_location";
	const CLIENT_COUNTRY		= "client_country";
	const CLIENT_LOGO			= "client_logo";
	const CLIENT_CREATED_AT		= "client_created_at";
	
	const USER_ID				= "user_id";
	const USERNAME				= "username";
	const USER_AVATAR			= "avatar";
	const USER_EMAIL			= "email";
	const USER_PHONE			= "phone";
	const USER_FIRSTNAME		= "firstname";
	const USER_LASTNAME			= "lastname";
	const USER_STREET			= "street";
	const USER_THEME			= "theme";
	const USER_HOUSE_NUMBER		= "house_number";
	const USER_ZIPCODE			= "zipcode";
	const USER_LOCATION			= "location";
	const USER_COUNTRY			= "country";
	const USER_LANGUAGE			= "language";
	const CREATED_AT			= "created_at";
	const LAST_LOGIN			= "last_login";
	const LAST_URL				= "last_url";
	
	// user-arrays
	const USER_ROLES			= "roles";	
	const USER_PERMISSIONS		= "permissions";
	const USER_MENU				= "menu";	
	const USER_MENU_SIDEBAR		= "menu_sidebar";
}

abstract class E_SIZES extends BASE_Enum
{
	const STANDARD 	= "";
	const XS 		= "xs";
	const SM 		= "sm";
	const MD 		= "md";
	const LG 		= "lg";
}

/**
 * @category helper
 * @package application\helpers\enum_helper
 * @final
 */
abstract class E_SORTABLE extends BASE_Enum
{
	const YES	= true;
	const NO	= false;
}

/**
 * Available Status codes for the BASE_Result
 * 
 * @see application/core/BASE_Result
 * 
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_STATUS_CODE extends BASE_Enum
{
	const DB_ERROR		= "DB-ERROR";	
	const ERROR			= "ERROR";
	const FORBIDDEN		= "403";
	const NOT_FOUND		= "404";
	const SUCCESS		= "SUCCESS";
}

/**
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_TASK_EVENT extends BASE_Enum
{
	const START		= "start";
	const STOP		= "stop";
	const NOTICE 	= "notice";
	const WARNING 	= "warning";
	const ERROR 	= "error";
}

/**
 * Bootstrap contextual colors
 * 
 * @link https://getbootstrap.com/docs/3.3/css/#responsive-utilities 
 * 
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.0
 */
abstract class E_TEXT_COLORS extends BASE_Enum
{
	const MUTED		= "text-muted";
	const PRIMARY	= "text-primary";
	const SUCCESS 	= "text-success";
	const INFO 		= "text-info";
	const WARNING 	= "text-warning";
	const DANGER 	= "text-danger";
}

/**
 * Available Bootstrap-Themes (Updated to version 3.3.7)
 * 
 * Special thx to bootswatch
 * @see https://bootswatch.com/
 * 
 * Create own themes at
 * @see visit http://stylebootstrap.info/  
 * 
 * @category helper
 * @package application\helpers\enum_helper
 * @version 3.3.7
 */
abstract class E_THEMES extends BASE_Enum
{
	const STANDARD		= "bootstrap-3.3.7.css";
	
	const CERULEAN		= "bootstrap-3.3.7_cerulean.css";
	const COSMO			= "bootstrap-3.3.7_cosmo.css";
	const CYBORG		= "bootstrap-3.3.7_cyborg.css";
	const DARKLY		= "bootstrap-3.3.7_darkly.css";
	const FLATLY		= "bootstrap-3.3.7_flatly.css";
	const JOURNAL		= "bootstrap-3.3.7_journal.css";
	const LUMEN			= "bootstrap-3.3.7_lumen.css";
	const PAPER			= "bootstrap-3.3.7_paper.css";
	const READABLE		= "bootstrap-3.3.7_readable.css";
	const SANDSTONE		= "bootstrap-3.3.7_sandstone.css";
	const SIMPLEX		= "bootstrap-3.3.7_simplex.css";
	const SLATE			= "bootstrap-3.3.7_slate.css";
	const SPACELAB		= "bootstrap-3.3.7_spacelab.css";
	const SUPERHERO		= "bootstrap-3.3.7_superhero.css";
	const UNITED		= "bootstrap-3.3.7_united.css";
	const YETI			= "bootstrap-3.3.7_yeti.css";
}

/**
 * @category helper
 * @package application\helpers\enum_helper
 * @final
 */
abstract class E_TOOLTIP_POSITION extends BASE_Enum
{
	const LEFT		= "left";
	const RIGHT		= "right";
	const TOP 		= "top";
	const BOTTOM 	= "bottom";
}

/**
 * @category helper
 * @package application\helpers\enum_helper
 * @final
 */
abstract class E_VISIBLE extends BASE_Enum
{
	const YES	= true;
	const NO	= false;
}

/**
 * @category helper
 * @package application\helpers\enum_helper
 * @final
 */
abstract class E_SEARCHABLE extends BASE_Enum
{
	const YES	= true;
	const NO	= false;
}

/**
 * All available Email-Templates (@see db-table app_templates__mail)
 *
 * @category helper
 * @package application\helpers\enum_helper
 * @version 1.1
 */
abstract class E_MAIL_TEMPLATES extends BASE_Enum
{
	const CONTACT_FORM 					= "CONTACT_FORM";
	const TASK_ERROR					= "TASK_ERROR";
	const TASK_FINISHED					= "TASK_FINISHED";
	const VERIFY_YOUR_EMAIL_ADDRESS		= "VERIFY_YOUR_EMAIL_ADDRESS";
}

/**
 * @category helper
 * @package application\helpers\enum_helper
 * @final
 */
abstract class E_MULTIPLE extends BASE_Enum
{
    const YES	= true;
    const NO	= false;
}
?>