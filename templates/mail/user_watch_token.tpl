<a href="mailto:{$email_token}">{$email_token}</a> {tr}has consulted your{/tr} 

{if $filegallery eq 'y'}
	{tr}file{/tr} : {$filename}
	<br />
	<br />
	<a href="{$prefix_url}/tiki-list_file_gallery.php?galleryId={$filegalleryId}">&raquo; {tr}Go to the File Gallery{/tr}</a><br />
	<a href="{$prefix_url}/tiki-download_file.php?fileId={$fileId}">&raquo; {tr}Download the file:{/tr} {$filename}</a><br />
{else}
	{tr}page{/tr} <a href="{$page_token}">{$page_token}</a>
{/if}
