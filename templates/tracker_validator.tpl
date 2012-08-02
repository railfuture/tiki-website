{* $Id: tracker_validator.tpl 42016 2012-06-20 17:30:31Z jonnybradley $ *}
{if isset($validationjs)}{jq}
$("#editItemForm{{$trackerEditFormId}}").validate({
	{{$validationjs}},
	ignore: '.ignore',
	submitHandler: function(){process_submit(this.currentForm);}
});
{/jq}{/if}
