<?
global $USER;
if($USER->IsAuthorized()) {
	$userId = $USER->GetID();
}

if($userId) {
	global $USER_FIELD_MANAGER; 
	$ufId = ($userId % 1000).($comment['ID'] % 1000);
	$fields = $USER_FIELD_MANAGER->GetUserFields("BLOG_COMMENT_ID", $ufId);
	$fieldValueLike = $fields['UF_LIKE_ID']['VALUE'];
	$fieldValueLike = unserialize($fieldValueLike);

	if( isset($fieldValueLike[$userId]) ) {
		$valuelike = $fieldValueLike[$userId];
	} else {
		$valuelike = 'N';
	}

	$bActiveLike = $valuelike == 'Y';

	$fieldValueDisLike = $fields['UF_DISLIKE_ID']['VALUE'];
	$fieldValueDisLike = unserialize($fieldValueDisLike);

	if( isset($fieldValueDisLike[$userId]) ) {
		$valuedislike = $fieldValueDisLike[$userId];
	} else {
		$valuedislike = 'N';
	}

	$bActiveDisLike = $valuedislike == 'Y';
}
?>
<span class="rating-vote <?=$userId ? 'active' : ''?>" data-comment_id="<?=$comment['ID']?>" data-user_id="<?=$userId?>" data-ajax_url="<?=str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__).'/ajaxLike.php'?>">
	<a class="rating_vote plus <?=$userId ? '' : 'disable'?> <?=$bActiveLike ? 'active' : ''?>" data-action="plus" title="<?=GetMessage('LIKE')?>">
		<?/*=CMax::showIconSvg("plus", SITE_TEMPLATE_PATH."/images/svg/like_like.svg");*/?>
		<i class="svg inline  svg-inline-plus" aria-hidden="true">
			<svg xmlns="http://www.w3.org/2000/svg" width="11.94" height="12.03" viewBox="0 0 11.94 12.03"><defs><style>.cls-1{fill:#333;fill-rule:evenodd;}</style></defs><path class="cls-1" d="M1187.68,6367l2.5-.34a2.856,2.856,0,0,0,2.1-2.26,2.11,2.11,0,0,0-.37-2.07,2.157,2.157,0,0,1,2.87.48c0.59,0.81-.15,2.87-0.15,2.87s-0.48.35,0.4,0.33c1.69-.03,4.32-0.04,3.84,2.12-0.54,2.48-.6,3.1-1,4.51a1.578,1.578,0,0,1-1.68,1.32c-0.31,0-2.48.08-4.51,0.01-1.01-.04-1.82-0.95-2.28-0.96a10.368,10.368,0,0,0-1.72-.01l-0.68-.76v-4.45Z" transform="translate(-1187 -6361.97)"></path></svg>
		</i>
	</a>
	<span class="rating-vote-result like">
		<?=intval($comment['UF_ASPRO_COM_LIKE'])?>
	</span>

	<a class="rating_vote minus <?=$userId ? '' : 'disable'?> <?=$bActiveDisLike ? 'active' : ''?>" data-action="minus" title="<?=GetMessage('DISLIKE')?>">
		<?/*=CMax::showIconSvg("minus", SITE_TEMPLATE_PATH."/images/svg/like_dislike.svg");*/?>
		<i class="svg inline  svg-inline-minus" aria-hidden="true">
			<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12"><defs><style>.cls-1{fill:#333;fill-rule:evenodd;}</style></defs><path data-name="Shape 949 copy" class="cls-1" d="M1231.69,6368.98l2.51,0.34a2.858,2.858,0,0,1,2.11,2.25,2.11,2.11,0,0,1-.37,2.07,2.181,2.181,0,0,0,2.88-.48c0.59-.81-0.15-2.87-0.15-2.87s-0.49-.34.4-0.33c1.7,0.04,4.34.05,3.87-2.1-0.55-2.48-.61-3.09-1.02-4.5a1.563,1.563,0,0,0-1.68-1.32c-0.31,0-2.49-.09-4.53-0.01-1.02.04-1.84,0.95-2.3,0.96a13.829,13.829,0,0,1-1.72.01l-0.69.75v4.45Z" transform="translate(-1231 -6362)"></path></svg>
		</i>
	</a>

	<span class="rating-vote-result dislike">
		<?=intval($comment['UF_ASPRO_COM_DISLIKE'])?>
	</span>
</span>

<script type="text/javascript">

</script>