<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	mod_modified
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$morc = $params->get('ordering','c_dsc');
?>
<table class="adminlist">
	<thead>
		<tr>
			<th>
				<?php echo JText::_('MOD_MODIFIED_LATEST_ITEMS'); ?>
			</th>
			<th>
				<strong><?php echo JText::_('JSTATUS'); ?></strong>
			</th>
			<th>
				<strong><?php echo ($morc == 'c_dsc' ? JText::_('MOD_MODIFIED_CREATED') : JText::_('MOD_MODIFIED_MODIFIED')); ?></strong>
			</th>
			<th>
				<strong><?php echo ($morc == 'c_dsc' ? JText::_('MOD_MODIFIED_CREATED_BY') : JText::_('MOD_MODIFIED_MODIFIED_BY'));?></strong>
			</th>
		</tr>
	</thead>
<?php if (count($list)) : ?>
	<tbody>
	<?php foreach ($list as $i=>$item) : ?>
		<tr>
			<th scope="row">
				<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time); ?>
				<?php endif; ?>

				<?php if ($item->link) :?>
					<a href="<?php echo $item->link; ?>">
						<?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8');?></a>
				<?php else :
					echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8');
				endif; ?>
			</th>
			<td class="center">
				<?php echo JHtml::_('jgrid.published', $item->state, $i, '', false); ?>
			</td>
			<td class="center">
				<?php echo ($morc == 'c_dsc' ? JHtml::_('date', $item->created, 'Y-m-d H:i:s') : JHtml::_('date', $item->modified, 'Y-m-d H:i:s')); ?>
			</td>
			<td class="center">
				<?php echo ($morc == 'c_dsc' ? $item->author_name : JFactory::getUser($item->modified_by)->name);?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
<?php else : ?>
	<tbody>
		<tr>
			<td colspan="4">
				<p class="noresults"><?php echo JText::_('MOD_MODIFIED_NO_MATCHING_RESULTS');?></p>
			</td>
		</tr>
	</tbody>
<?php endif; ?>
</table>
