<?php
use com\cminds\siteaccessrestriction\controller\UrlController;
use com\cminds\siteaccessrestriction\helper\UrlRestriction;
use com\cminds\siteaccessrestriction\helper\RestrictionSettings;

$displayRow = function($index, $filter = null) {
	?><tr class="<?php echo ($filter ? 'cmacc-url-filter' : 'cmacc-template'); ?>" data-id="<?php echo ($filter ? $filter->getId() : 0); ?>" data-index="<?php echo $index; ?>">
		<td class="title column-title has-row-actions column-primary page-title">
			<input type="text" name="filters[url][<?php echo $index; ?>]" value="<?php if ($filter) echo esc_attr($filter->getUrl()); ?>" />
		</td>
		<td class="column-access">
			<?php echo RestrictionSettings::displaySettings(
				new UrlRestriction($filter),
				$restrictionFieldName = 'filters[restriction]['. $index .']',
				$roleFieldName = 'filters[roles]['. $index .']',

                $allowedUsersFieldName = 'filters[allowed_users][' . $index . ']',
                $notAllowedUsersFieldName = 'filters[not_allowed_users][' . $index . ']',

                $daysFieldName = 'filters[days][' . $index . ']',
                $daysFromFirstAccessFieldName = 'filters[days_from_first_access][' . $index . ']',
                $fromDateFieldName = 'filters[from_date][' . $index . ']',
                $toDateFieldName = 'filters[to_date][' . $index . ']'
			); ?>
			<input type="hidden" name="filters[id][<?php echo $index; ?>]" value="<?php echo esc_attr($filter ? $filter->getId() : ''); ?>" />
		</td>
		<td class="column-delete">
			<a href="<?php if ($filter) echo esc_attr($filter->getDeleteUrl()); ?>" class="button">Delete</a>
		</td>
	</tr><?php
};
?>
<h4>Instructions:</h4>
<ul>
	<li>Enter the exact URL address you want to match without the hostname part eg. <kbd>/user-profile/</kbd>.</li>
	<li>You can use wildcard * which means that there could be any string or not.</li>
	<li>You can use regular expressions wrapped between ~tildes~ eg. <kbd>~/profile/.+~</kbd>.</li>
</ul>
<form class="cmacc-url-filters" method="post">
	<p><button class="button cmacc-add-url-filter-btn">Add new filter</button></p>
	<table class="wp-list-table widefat fixed striped posts">
		<thead>
			<tr>
				<th>URL</th>
				<th>Access Restriction</th>
				<th style="width:5em">Delete</th>
			</tr>
		</thead>
		<tbody>
			<?php $index = 0; ?>
			<?php $displayRow($index, null); ?>
			<?php if (!empty($filters)) foreach ($filters as $id => $filter): ?>
				<?php $index++; ?>
				<?php $displayRow($index, $filter); ?>
			<?php endforeach; ?>
		</tbody>
	</table>
	<p class="submit">
		<input type="hidden" name="<?php echo UrlController::PARAM_ACTION; ?>" value="<?php echo UrlController::ACTION_SAVE; ?>" />
		<input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
		<button type="submit" class="button button-primary">Save filters</button>
	</p>
</form>