<?php
include 'includes/autoload.inc.php';

unset($_SESSION['title']);
unset($_SESSION['Active_Navigate']);
$_SESSION['title'] = 'Report';
$_SESSION['Active_Navigate'] = 'Report';

include_once './includes/header.php';
include_once './includes/navbar.php';
?>

<div class="container-fluid px-0">
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 py-3">
        <div>
            <h2 class="h4 mb-1">Age Category Report</h2>
            <div class="text-muted small" id="ageReportSummary" aria-live="polite">Loading residents...</div>
        </div>
        <div class="d-flex flex-wrap align-items-end gap-2">
            <div>
                <label for="ageReportCategory" class="form-label mb-1">Age category</label>
                <select class="form-select" id="ageReportCategory">
                    <option value="all">All categories</option>
                    <option value="0">Infants: 0-1 year</option>
                    <option value="2">Toddlers: 2-3 years</option>
                    <option value="4">Preschoolers: 4-5 years</option>
                    <option value="6">Middle Childhood: 6-11 years</option>
                    <option value="12">Adolescents / Teenagers: 12-19 years</option>
                    <option value="20">Young Adults: 20-39 years</option>
                    <option value="40">Middle-Aged Adults: 40-59 years</option>
                    <option value="60">Seniors / Older Adults: 60+ years</option>
                </select>
            </div>
            <button type="button" class="btn btn-outline-secondary" id="refreshAgeReport" title="Refresh report" aria-label="Refresh report">
                <i class="fas fa-sync-alt" aria-hidden="true"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary" id="printAgeReport" title="Print report" aria-label="Print report">
                <i class="fas fa-print" aria-hidden="true"></i>
            </button>
        </div>
    </div>
    <div id="ageReportContent" aria-live="polite"></div>
</div>
<script src="./js/age-category-report.js" defer></script>
<?php include_once './includes/footer.php';
?>