<div class="navbar"> {button href="tiki-directory_browse.php" _text="{tr}Browse{/tr}"}
	{button href="tiki-directory_admin.php" _text="{tr}Admin{/tr}"}
	
	{if $tiki_p_admin_directory_cats eq 'y'}
		{button href="tiki-directory_admin_categories.php" _text="{tr}Directory Categories{/tr}"}
	{/if}
	
	{if $tiki_p_admin_directory_cats eq 'y'}
		{button href="tiki-directory_admin_related.php" _text="{tr}Related{/tr}"}
	{/if}
	
	{if $tiki_p_admin_directory_sites eq 'y'}
		{button href="tiki-directory_admin_sites.php" _text="{tr}Sites{/tr}"}
	{/if}
	
	{if $tiki_p_validate_links eq 'y'}
		{button href="tiki-directory_validate_sites.php" _text="{tr}Validate{/tr}"}
	{/if}
</div>
