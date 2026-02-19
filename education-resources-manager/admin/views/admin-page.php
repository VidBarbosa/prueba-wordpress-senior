<?php
/**
 * Admin dashboard view.
 *
 * @var array $filters
 * @var array $resources
 * @var array $top_resources
 * @var array $resources_by_type
 * @var array $month_stats
 * @var array $categories
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap erm-admin-wrap">
	<h1><?php esc_html_e( 'Panel de Recursos Educativos', 'education-resources-manager' ); ?></h1>

	<form method="get" class="erm-admin-filters">
		<input type="hidden" name="page" value="erm-admin" />
		<select name="type">
			<option value=""><?php esc_html_e( 'Todos los tipos', 'education-resources-manager' ); ?></option>
			<option value="curso" <?php selected( $filters['type'], 'curso' ); ?>>Curso</option>
			<option value="tutorial" <?php selected( $filters['type'], 'tutorial' ); ?>>Tutorial</option>
			<option value="ebook" <?php selected( $filters['type'], 'ebook' ); ?>>eBook</option>
			<option value="video" <?php selected( $filters['type'], 'video' ); ?>>Video</option>
		</select>
		<select name="level">
			<option value=""><?php esc_html_e( 'Todos los niveles', 'education-resources-manager' ); ?></option>
			<option value="principiante" <?php selected( $filters['level'], 'principiante' ); ?>>Principiante</option>
			<option value="intermedio" <?php selected( $filters['level'], 'intermedio' ); ?>>Intermedio</option>
			<option value="avanzado" <?php selected( $filters['level'], 'avanzado' ); ?>>Avanzado</option>
		</select>
		<select name="category">
			<option value="0"><?php esc_html_e( 'Todas las categorías', 'education-resources-manager' ); ?></option>
			<?php foreach ( $categories as $category ) : ?>
				<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( $filters['category'], (int) $category->term_id ); ?>>
					<?php echo esc_html( $category->name ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<button class="button button-primary" type="submit"><?php esc_html_e( 'Filtrar', 'education-resources-manager' ); ?></button>
	</form>

	<div class="erm-grid">
		<div class="erm-card">
			<h2><?php esc_html_e( 'Totales por tipo', 'education-resources-manager' ); ?></h2>
			<ul>
				<?php foreach ( $resources_by_type as $type => $total ) : ?>
					<li><strong><?php echo esc_html( ucfirst( $type ) ); ?>:</strong> <?php echo esc_html( $total ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="erm-card">
			<h2><?php esc_html_e( 'Top 5 más visualizados', 'education-resources-manager' ); ?></h2>
			<ol>
				<?php foreach ( $top_resources as $item ) : ?>
					<li>
						<?php echo esc_html( get_the_title( (int) $item['resource_id'] ) ); ?>
						(<?php echo esc_html( (int) $item['total'] ); ?>)
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</div>

	<div class="erm-card">
		<h2><?php esc_html_e( 'Recursos creados por mes (últimos 6 meses)', 'education-resources-manager' ); ?></h2>
		<table class="widefat">
			<thead><tr><th>Mes</th><th>Total</th></tr></thead>
			<tbody>
			<?php foreach ( $month_stats as $row ) : ?>
				<tr><td><?php echo esc_html( $row['ym'] ); ?></td><td><?php echo esc_html( $row['total'] ); ?></td></tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<div class="erm-card">
		<h2><?php esc_html_e( 'Listado de recursos', 'education-resources-manager' ); ?></h2>
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Título', 'education-resources-manager' ); ?></th>
					<th><?php esc_html_e( 'Tipo', 'education-resources-manager' ); ?></th>
					<th><?php esc_html_e( 'Nivel', 'education-resources-manager' ); ?></th>
					<th><?php esc_html_e( 'Estado', 'education-resources-manager' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $resources as $resource ) : ?>
					<tr>
						<td><a href="<?php echo esc_url( get_edit_post_link( $resource->ID ) ); ?>"><?php echo esc_html( $resource->post_title ); ?></a></td>
						<td><?php echo esc_html( get_post_meta( $resource->ID, '_erm_resource_type', true ) ); ?></td>
						<td><?php echo esc_html( get_post_meta( $resource->ID, '_erm_difficulty_level', true ) ); ?></td>
						<td><?php echo esc_html( get_post_meta( $resource->ID, '_erm_resource_status', true ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
