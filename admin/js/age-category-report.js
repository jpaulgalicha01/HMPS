const ageCategories = [
    { id: 0, label: 'Infants: 0-1 year' },
    { id: 2, label: 'Toddlers: 2-3 years' },
    { id: 4, label: 'Preschoolers: 4-5 years' },
    { id: 6, label: 'Middle Childhood: 6-11 years' },
    { id: 12, label: 'Adolescents / Teenagers: 12-19 years' },
    { id: 20, label: 'Young Adults: 20-39 years' },
    { id: 40, label: 'Middle-Aged Adults: 40-59 years' },
    { id: 60, label: 'Seniors / Older Adults: 60+ years' }
];

let residentRows = [];

$(document).ready(() => {
    const categorySelect = document.getElementById('ageReportCategory');
    const refreshButton = document.getElementById('refreshAgeReport');
    const printButton = document.getElementById('printAgeReport');

    if (!categorySelect || !refreshButton || !printButton) {
        throw new Error('Age category report controls are missing from the loaded page.');
    }

    categorySelect.addEventListener('change', renderReport);
    refreshButton.addEventListener('click', loadReport);
    printButton.addEventListener('click', () => window.print());
    loadReport();
})

async function loadReport() {
    const content = document.getElementById('ageReportContent');
    const summary = document.getElementById('ageReportSummary');
    content.innerHTML = '<div class="py-4 text-center text-muted">Loading report...</div>';
    summary.textContent = 'Loading residents...';

    try {
        const response = await fetch('inputConfig.php?getAgePurokReport=true', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-cache'
        });
        const result = await response.json();

        if (!response.ok || result.status !== 200 || !Array.isArray(result.data)) {
            throw new Error(result.message || 'Unable to load the age category report.');
        }

        residentRows = result.data;
        renderReport();
    } catch (error) {
        summary.textContent = 'Report could not be loaded.';
        content.innerHTML = `<div class="alert alert-danger mb-0">${escapeHtml(error.message)}</div>`;
        console.error('[AgeCategoryReport] Failed to load report:', error);
    }
}

function renderReport() {
    const content = document.getElementById('ageReportContent');
    const summary = document.getElementById('ageReportSummary');
    const selectedCategory = document.getElementById('ageReportCategory').value;
    const filteredRows = selectedCategory === 'all'
        ? residentRows
        : residentRows.filter(row => String(row.category_id) === selectedCategory);

    summary.textContent = `${filteredRows.length} resident${filteredRows.length === 1 ? '' : 's'} in report`;

    if (filteredRows.length === 0) {
        content.innerHTML = '<div class="border-top py-4 text-center text-muted">No residents found for this age category.</div>';
        return;
    }

    const grouped = new Map();
    filteredRows.forEach(row => {
        const categoryKey = String(row.category_id);
        if (!grouped.has(categoryKey)) {
            grouped.set(categoryKey, new Map());
        }

        const purokGroups = grouped.get(categoryKey);
        if (!purokGroups.has(row.purok_sitio_name)) {
            purokGroups.set(row.purok_sitio_name, []);
        }
        purokGroups.get(row.purok_sitio_name).push(row);
    });

    content.innerHTML = [...grouped.entries()].map(([categoryId, puroks]) => {
        const category = ageCategories.find(item => String(item.id) === categoryId);
        const categoryCount = [...puroks.values()].reduce((total, residents) => total + residents.length, 0);
        const purokSections = [...puroks.entries()].map(([purokName, residents]) => `
            <section class="border-top py-3 age-report-purok">
                <div class="d-flex justify-content-between align-items-baseline gap-2 mb-2">
                    <h4 class="h6 mb-0">${escapeHtml(purokName)}</h4>
                    <span class="small text-muted">${residents.length} resident${residents.length === 1 ? '' : 's'}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-bordered mb-0">
                        <thead><tr><th scope="col">Name</th><th scope="col">Age</th><th scope="col">Household No.</th></tr></thead>
                        <tbody>${residents.map(resident => `
                            <tr>
                                <td>${escapeHtml(resident.full_name)}</td>
                                <td>${escapeHtml(resident.age_years)} years</td>
                                <td>${escapeHtml(resident.household_number || 'Not assigned')}</td>
                            </tr>`).join('')}
                        </tbody>
                    </table>
                </div>
            </section>`).join('');

        return `
            <section class="age-report-category py-3" data-category="${categoryId}">
                <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-2">
                    <h3 class="h5 mb-0">${escapeHtml(category?.label || 'Age category')}</h3>
                    <span class="small text-muted">${categoryCount} resident${categoryCount === 1 ? '' : 's'}</span>
                </div>
                ${purokSections}
            </section>`;
    }).join('');
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    })[character]);
}