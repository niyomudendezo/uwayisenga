<?php
$title = $coop['name'] . ' — Members';
$memberCount = count($members);
$producingMembers = count(array_filter($harvestStats));
$totalHarvest = array_sum(array_column($harvestStats, 'total_qty'));
$totalArea = array_sum(array_column($members, 'farm_size'));
$participationRate = $memberCount > 0 ? round(($producingMembers / $memberCount) * 100) : 0;
?>
<div class="coop-members-page">
  <nav class="member-breadcrumb" aria-label="Breadcrumb"><a href="<?= APP_URL ?>/admin/cooperatives"><i class="bi bi-buildings"></i> Cooperatives</a><i class="bi bi-chevron-right"></i><span>Member directory</span></nav>
  <header class="member-profile-header">
    <div class="member-profile-main"><span class="member-coop-mark"><?= strtoupper(substr($coop['name'], 0, 1)) ?></span><div><span class="dashboard-eyebrow">Cooperative profile</span><h1><?= Helper::e($coop['name']) ?></h1><div class="member-profile-meta">
      <span><i class="bi bi-geo-alt"></i><?= Helper::e($coop['district_name'] ?? 'Location not specified') ?></span>
      <?php if ($coop['registration_no']): ?><span><i class="bi bi-patch-check"></i><?= Helper::e($coop['registration_no']) ?></span><?php endif; ?>
      <span class="member-status member-status-<?= Helper::e($coop['status']) ?>"><i></i><?= ucfirst(Helper::e($coop['status'])) ?></span>
    </div></div></div>
    <a href="<?= APP_URL ?>/admin/cooperatives" class="member-back-btn"><i class="bi bi-arrow-left"></i> Back to cooperatives</a>
  </header>
  <section class="member-info-strip">
    <div><span class="member-info-icon"><i class="bi bi-person-badge"></i></span><p>Cooperative manager<strong><?= $coop['first_name'] ? Helper::e($coop['first_name'].' '.$coop['last_name']) : 'Not assigned' ?></strong></p></div>
    <div><span class="member-info-icon"><i class="bi bi-envelope"></i></span><p>Manager contact<strong><?= !empty($coop['manager_email']) ? Helper::e($coop['manager_email']) : 'No email available' ?></strong></p></div>
    <div><span class="member-info-icon"><i class="bi bi-calendar3"></i></span><p>Registered on<strong><?= !empty($coop['created_at']) ? date('d F Y', strtotime($coop['created_at'])) : 'Not recorded' ?></strong></p></div>
  </section>
  <section class="member-summary" aria-label="Member and production summary">
    <article><span class="member-summary-icon summary-green"><i class="bi bi-people"></i></span><div><strong><?= number_format($memberCount) ?></strong><span>Total members</span></div><small>Registered farmers</small></article>
    <article><span class="member-summary-icon summary-blue"><i class="bi bi-bar-chart-line"></i></span><div><strong><?= number_format($producingMembers) ?></strong><span>Active producers</span></div><small><?= $participationRate ?>% participation</small></article>
    <article><span class="member-summary-icon summary-amber"><i class="bi bi-basket2"></i></span><div><strong><?= number_format($totalHarvest) ?> <em>kg</em></strong><span>Total harvest</span></div><small>Recorded production</small></article>
    <article><span class="member-summary-icon summary-purple"><i class="bi bi-bounding-box"></i></span><div><strong><?= number_format($totalArea, 1) ?> <em>ha</em></strong><span>Farm area</span></div><small>Combined farmland</small></article>
  </section>
  <section class="member-directory-card">
    <div class="member-directory-head"><div><h2>Member directory</h2><p>Farmer profiles and production activity</p></div><div class="member-directory-tools"><label class="member-search"><i class="bi bi-search"></i><input type="search" id="coopMemberSearch" placeholder="Search members or farms" aria-label="Search members" autocomplete="off"></label><span class="member-count-pill"><i class="bi bi-people"></i><span id="memberVisibleCount"><?= $memberCount ?></span> <span id="memberCountLabel">member<?= $memberCount === 1 ? '' : 's' ?></span></span></div></div>
    <div class="table-responsive"><table class="table member-directory-table align-middle mb-0" id="coopMembersTable"><thead><tr><th>Member</th><th>Contact</th><th>Farm</th><th>Land & soil</th><th>Irrigation</th><th>Harvest activity</th><th>Production</th><th>Status</th></tr></thead><tbody>
      <?php foreach ($members as $index => $member): $harvest = $harvestStats[$member['farmer_id']] ?? null; ?>
      <tr>
        <td><div class="member-identity"><span class="member-avatar member-avatar-<?= ($index % 4) + 1 ?>"><?= strtoupper(substr($member['first_name'],0,1).substr($member['last_name'],0,1)) ?></span><div><strong><?= Helper::e($member['first_name'].' '.$member['last_name']) ?></strong><small>Joined <?= !empty($member['joined_at']) ? date('M Y', strtotime($member['joined_at'])) : '—' ?></small></div></div></td>
        <td><div class="member-contact"><span><i class="bi bi-envelope"></i><?= Helper::e($member['email']) ?></span><span><i class="bi bi-telephone"></i><?= Helper::e($member['phone'] ?? 'No phone') ?></span></div></td>
        <td><span class="member-farm-name"><i class="bi bi-house-heart"></i><?= Helper::e($member['farm_name'] ?? 'Unnamed farm') ?></span></td>
        <td><div class="member-land"><strong><?= $member['farm_size'] ? number_format($member['farm_size'],1).' ha' : '—' ?></strong><small><?= Helper::e($member['soil_type'] ?? 'Soil not specified') ?></small></div></td>
        <td><?php if (!empty($member['irrigation'])): ?><span class="irrigation-badge has-irrigation"><i class="bi bi-droplet-fill"></i> Available</span><?php else: ?><span class="irrigation-badge"><i class="bi bi-droplet"></i> None</span><?php endif; ?></td>
        <td><?php if ($harvest): ?><div class="harvest-activity"><span><i class="bi bi-check2"></i></span><div><strong><?= (int)$harvest['harvest_count'] ?> record<?= (int)$harvest['harvest_count'] === 1 ? '' : 's' ?></strong><small>Harvest submitted</small></div></div><?php else: ?><span class="no-harvest"><i class="bi bi-dash-circle"></i> No records</span><?php endif; ?></td>
        <td><strong class="member-production"><?= $harvest ? number_format($harvest['total_qty']).' kg' : '—' ?></strong></td>
        <td><span class="coop-status coop-status-<?= Helper::e($member['status'] ?? 'active') ?>"><i></i><?= ucfirst(Helper::e($member['status'] ?? 'active')) ?></span></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($members)): ?><tr><td colspan="8"><div class="member-empty"><span><i class="bi bi-people"></i></span><h3>No members yet</h3><p>This cooperative does not have any registered farmer members.</p></div></td></tr><?php endif; ?>
    </tbody></table><div class="member-no-results" id="memberNoResults"><i class="bi bi-search"></i><strong>No matching members</strong><span>Try a different name, email, phone number or farm.</span></div></div>
    <?php if ($memberCount): ?><footer class="member-directory-footer"><span><i class="bi bi-info-circle"></i> Production totals are calculated from all recorded member harvests.</span><span>Last viewed <?= date('d M Y') ?></span></footer><?php endif; ?>
  </section>
</div>
