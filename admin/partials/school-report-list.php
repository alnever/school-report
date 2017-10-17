<div class="wrap">
	<h2>
    <?php echo $this->plugin_name.": ".get_admin_page_title(); ?>
    <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page='.$this->list_view->get_table_key().'-form');?>">
      <?php _e('Добавить', 'school-report-add-form')?>
    </a>
  </h2>


	<?php if (!empty($this->list_view->get_message())): ?>
	<?php	print_r($this->list_view->get_message()); ?>
	<div id="message" class="updated"><p><?php echo $this->list_view->get_message(); ?></p></div>
	<?php endif;?>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<form method="post">
            <?php
						$this->list_view->prepare_items();
						$this->list_view->display();
            ?>
					</form>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
</div>
