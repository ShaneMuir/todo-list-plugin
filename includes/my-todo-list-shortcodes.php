<?php
	// List Todos
	function mtl_list_todos($atts, $content = null): string {
		global $post;
		// Create attributes and defaults
		$atts = shortcode_atts(array(
			'title' => 'My Todos',
			'count' => 10,
			'category' => 'all'
		), $atts);

		// Check category attribute
		if($atts['category'] == 'all'){
			$terms = '';
		} else {
			$terms = array(
				array(
					'taxonomy' => 'category',
					'field'	=> 'slug',
					'terms'	=> $atts['category']
				)
			);
		}

		// Query Args
		$args = array(
			'post_type'			=> 'todo',
			'post_status'		=> 'publish',
			'orderby'			=> 'due_date',
			'order'				=> 'ASC',
			'posts_per_page'	=> $atts['count'],
			'tax_query'			=> $terms
		);

		// Fetch Todos
		$todos = new WP_Query($args);

		// Check For Todos
		if($todos->have_posts()){
			// Get Category Slug
			$category = str_replace('-',' ', $atts['category']);
			$category = strtolower($category);

			// Initialize output buffer
			ob_start();

			// Build Output
			?>
			<div class="todo-list">
				<?php while ($todos->have_posts()): $todos->the_post(); ?>
					<?php
					// Get Field values
					$priority = get_post_meta(get_the_ID(), 'priority', true) ?? 'low';
					$details = get_post_meta(get_the_ID(), 'details', true) ?? 'No details available';
					$due_date = get_post_meta(get_the_ID(), 'due_date', true) ?? 'No due date';
					?>
					<div class="todo">
						<h4><?php echo esc_html(get_the_title()); ?></h4>
						<div><?php echo esc_html($details); ?></div>
						<div class="priority-<?php echo esc_attr(strtolower($priority)); ?>">Priority: <?php echo esc_html($priority); ?></div>
						<div class="due_date">Due Date: <?php echo esc_html($due_date); ?></div>
					</div>
				<?php endwhile; ?>
			</div>
			<?php

			// Capture the output
			$output = ob_get_clean();


			// Reset Post Data
			wp_reset_postdata();

			return $output;
		} else {
			return '<p>No Todos</p>';
		}
	}

	// Todo List Shortcode
	add_shortcode('todos', 'mtl_list_todos');