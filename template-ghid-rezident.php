<?php
/**
 * Template Name: Ghidul Rezidentului
 *
 * @package Brezoaele_V2
 */

get_header();
?>

<main id="primary" class="site-main" style="padding: 40px 0; background-color: var(--color-bg);">
	<div class="container" style="max-width: 800px;">
		
		<header class="page-header" style="margin-bottom: 30px; text-align: center;">
			<h1 class="page-title" style="font-size: 2.5rem; margin-bottom: 8px; font-weight: 800; font-family: var(--font-heading);"><?php the_title(); ?></h1>
			<div style="width: 50px; height: 3px; background-color: var(--color-primary); margin: 12px auto 0 auto; border-radius: 3px;"></div>
		</header>

		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				
				$content = apply_filters( 'the_content', get_the_content() );
				
				// Parsăm toate elementele H2 pentru acordeon
				preg_match_all( '/<h2[^>]*>(.*?)<\/h2>/i', $content, $matches, PREG_OFFSET_CAPTURE );
				
				if ( ! empty( $matches[0] ) ) {
					// Obținem introducerea (tot ce este înainte de primul <h2>)
					$intro = trim( substr( $content, 0, $matches[0][0][1] ) );
					if ( ! empty( $intro ) ) {
						echo '<div class="page-intro" style="margin-bottom: 30px; line-height: 1.6; color: var(--color-text-muted); font-size: 1.05rem; text-align: center;">' . $intro . '</div>';
					}
					
					$sections = array();
					$num_matches = count( $matches[0] );
					
					for ( $i = 0; $i < $num_matches; $i++ ) {
						$title = $matches[1][$i][0];
						$start = $matches[0][$i][1] + strlen( $matches[0][$i][0] );
						
						if ( $i < $num_matches - 1 ) {
							$end = $matches[0][$i+1][1];
						} else {
							$end = strlen( $content );
						}
						
						$body = substr( $content, $start, $end - $start );
						$sections[] = array(
							'title' => $title,
							'body'  => trim( $body )
						);
					}
					
					echo '<div class="accordion-group">';
					foreach ( $sections as $section ) :
					?>
						<details>
							<summary><?php echo $section['title']; ?></summary>
							<div class="accordion-content">
								<?php echo $section['body']; ?>
							</div>
						</details>
					<?php
					endforeach;
					echo '</div>';
				} else {
					// Fallback dacă nu se găsește niciun H2 (randare clasică a conținutului)
					echo '<div style="line-height: 1.6; color: var(--color-text-dark);">' . $content . '</div>';
				}
				
			endwhile;
		endif;
		?>
		
	</div>
</main>

<?php
get_footer();
