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
	<div class="erm-admin-hero">
		<h1><?php esc_html_e( 'Panel de Recursos Educativos', 'education-resources-manager' ); ?></h1>
		<p><?php esc_html_e( 'Vista rápida del catálogo, métricas y filtros de gestión en un diseño limpio y moderno.', 'education-resources-manager' ); ?></p>
	</div>

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

	<div class="erm-kpis">
		<?php foreach ( $resources_by_type as $type => $total ) : ?>
			<div class="erm-kpi-card">
				<p class="erm-kpi-label"><?php echo esc_html( ucfirst( $type ) ); ?></p>
				<p class="erm-kpi-value"><?php echo esc_html( $total ); ?></p>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="erm-grid">
		<div class="erm-card">
			<h2><?php esc_html_e( 'Top 5 más visualizados', 'education-resources-manager' ); ?></h2>
			<ol class="erm-top-list">
				<?php if ( empty( $top_resources ) ) : ?>
					<li><?php esc_html_e( 'Sin datos de visualización aún.', 'education-resources-manager' ); ?></li>
				<?php else : ?>
					<?php foreach ( $top_resources as $item ) : ?>
						<li>
							<span><?php echo esc_html( get_the_title( (int) $item['resource_id'] ) ); ?></span>
							<strong><?php echo esc_html( (int) $item['total'] ); ?></strong>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>
			</ol>
		</div>
		<div class="erm-card">
			<h2><?php esc_html_e( 'Recursos creados por mes (6 meses)', 'education-resources-manager' ); ?></h2>
			<canvas id="erm-month-chart" height="210" aria-label="Gráfico de recursos por mes"></canvas>
			<table class="widefat striped erm-month-table">
				<thead><tr><th><?php esc_html_e( 'Mes', 'education-resources-manager' ); ?></th><th><?php esc_html_e( 'Total', 'education-resources-manager' ); ?></th></tr></thead>
				<tbody>
				<?php foreach ( $month_stats as $row ) : ?>
					<tr><td><?php echo esc_html( $row['ym'] ); ?></td><td><?php echo esc_html( $row['total'] ); ?></td></tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
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
				<?php if ( empty( $resources ) ) : ?>
					<tr><td colspan="4"><?php esc_html_e( 'No se encontraron recursos con los filtros actuales.', 'education-resources-manager' ); ?></td></tr>
				<?php else : ?>
					<?php foreach ( $resources as $resource ) : ?>
						<tr>
							<td><a href="<?php echo esc_url( get_edit_post_link( $resource->ID ) ); ?>"><?php echo esc_html( $resource->post_title ); ?></a></td>
							<td><span class="erm-badge"><?php echo esc_html( get_post_meta( $resource->ID, '_erm_resource_type', true ) ); ?></span></td>
							<td><?php echo esc_html( get_post_meta( $resource->ID, '_erm_difficulty_level', true ) ); ?></td>
							<td><?php echo esc_html( get_post_meta( $resource->ID, '_erm_resource_status', true ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
