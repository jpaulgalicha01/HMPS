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
    const purokSelect = document.getElementById('ageReportPurok');
    const refreshButton = document.getElementById('refreshAgeReport');
    const printButton = document.getElementById('printAgeReport');
    const content = document.getElementById('ageReportContent');

    if (!categorySelect || !purokSelect || !refreshButton || !printButton || !content) {
        throw new Error('Age category report controls are missing from the loaded page.');
    }

    categorySelect.addEventListener('change', renderReport);
    purokSelect.addEventListener('change', renderReport);
    refreshButton.addEventListener('click', loadReport);
    printButton.addEventListener('click', () => window.print());
    content.addEventListener('click', async event => {
        const button = event.target.closest('.print-resident-info');
        if (!button) return;

        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            window.alert('Allow pop-ups to print resident information.');
            return;
        }

        button.disabled = true;
        try {
            const body = new URLSearchParams({
                get_records_person: 'true',
                person_unique_id: button.dataset.personId
            });
            const response = await fetch('inputConfig.php', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body
            });
            const result = await response.json();

            if (!response.ok || result.status !== 200 || !result.data?.[0]) {
                throw new Error(result.message || 'Unable to load this resident’s personal information.');
            }

            printPersonalInformation(printWindow, result.data[0]);
        } catch (error) {
            printWindow.close();
            console.error('[AgeCategoryReport] Failed to load resident information:', error);
            window.alert(error.message);
        } finally {
            button.disabled = false;
        }
    });
    loadReport();
})

async function loadReport() {
    const content = document.getElementById('ageReportContent');
    const summary = document.getElementById('ageReportSummary');
    content.innerHTML = '<div class="py-4 text-center text-muted">Loading report...</div>';
    summary.textContent = 'Loading residents...';

    try {
        const headers = { 'X-Requested-With': 'XMLHttpRequest' };
        const [reportResponse, purokResponse] = await Promise.all([
            fetch('inputConfig.php?getAgePurokReport=true', { headers, cache: 'no-cache' }),
            fetch('inputConfig.php?getAllPurokSitioList=true', { headers, cache: 'no-cache' })
        ]);
        const [result, purokResult] = await Promise.all([
            reportResponse.json(),
            purokResponse.json()
        ]);

        if (!reportResponse.ok || result.status !== 200 || !Array.isArray(result.data)) {
            throw new Error(result.message || 'Unable to load the age category report.');
        }
        if (!purokResponse.ok || purokResult.status !== 200 || !Array.isArray(purokResult.data)) {
            throw new Error(purokResult.message || 'Unable to load Purok options from the database.');
        }

        residentRows = result.data;
        populatePurokFilter(purokResult.data);
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
    const selectedPurok = document.getElementById('ageReportPurok').value;
    const filteredRows = residentRows.filter(row => {
        const matchesCategory = selectedCategory === 'all' || String(row.category_id) === selectedCategory;
        const purokKey = row.purok_sitio_id == null ? 'unassigned' : String(row.purok_sitio_id);
        const matchesPurok = selectedPurok === 'all' || purokKey === selectedPurok;
        return matchesCategory && matchesPurok;
    });

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
                        <thead><tr><th scope="col">Name</th><th scope="col">Age</th><th scope="col">Household No.</th><th scope="col">Personal Information</th></tr></thead>
                        <tbody>${residents.map(resident => `
                            <tr>
                                <td>${escapeHtml(resident.full_name)}</td>
                                <td>${escapeHtml(resident.age_years)} years</td>
                                <td>${escapeHtml(resident.household_number || 'Not assigned')}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary print-resident-info"
                                        data-person-id="${escapeHtml(resident.person_unique_id)}"
                                        title="Print personal information" aria-label="Print personal information for ${escapeHtml(resident.full_name)}">
                                        <i class="fas fa-print" aria-hidden="true"></i>
                                    </button>
                                </td>
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

function populatePurokFilter(puroks) {
    const select = document.getElementById('ageReportPurok');
    const selectedValue = select.value;
    select.innerHTML = '<option value="all">All puroks</option>' +
        puroks
            .sort((a, b) => String(a.purok_sitio_name).localeCompare(String(b.purok_sitio_name)))
            .map(purok => `<option value="${escapeHtml(purok.purok_sitio_id)}">${escapeHtml(purok.purok_sitio_name)}</option>`)
            .join('');
    select.value = puroks.some(purok => String(purok.purok_sitio_id) === selectedValue)
        ? selectedValue
        : 'all';
}

function printPersonalInformation(printWindow, person) {
    const fields = [
        ['PhilSys Card No', person.phil_sys_id, 'wide'],
        ['Last Name', person.last_name],
        ['First Name', person.first_name],
        ['Middle Name', person.middle_name],
        ['Suffix', person.suffix],
        ['Birth Date', formatBirthDate(person.birdthdate)],
        ['Birth Place', person.birth_place],
        ['Sex', person.sex],
        ['Civil Status', person.civil_status],
        ['Religion', person.religion],
        ['Residential Address', person.residential_address, 'wide'],
        ['Citizenship', person.citizenship],
        ['Profession/Occupation', person.profession],
        ['Contact No.', person.contact_no],
        ['Email Address', person.email_address],
        ['Highest Educational Attainment', person.highest_attainment_education],
        ['Please Specify', person.highest_attainment_education_specific],
        ['Type of Disability', person.type_of_disability],
        ['Disability Details', person.type_of_disability_others, 'wide']
    ];
    const fullName = [person.first_name, person.middle_name, person.last_name, person.suffix]
        .filter(Boolean)
        .join(' ');
    const fieldsHtml = fields.map(([label, value, size]) => `
        <div class="field ${size === 'wide' ? 'wide' : ''}">
            <div class="label">${escapeHtml(label)}</div>
            <div class="value">${escapeHtml(value || 'N/A')}</div>
        </div>`).join('');

    printWindow.document.open();
    printWindow.document.write(`<!doctype html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Personal Information - ${escapeHtml(fullName)}</title>
            <style>
                * { box-sizing: border-box; }
                body { margin: 0; padding: 28px; color: #212529; font: 14px Arial, sans-serif; }
                h1 { margin: 0 0 22px; padding-bottom: 10px; border-bottom: 2px solid #198754; font-size: 22px; }
                .fields { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px 12px; }
                .field { min-width: 0; }
                .field.wide { grid-column: 1 / -1; }
                .label { margin-bottom: 5px; color: #495057; font-size: 12px; }
                .value { min-height: 34px; padding: 9px 10px; border: 1px solid #ced4da; border-radius: 4px; overflow-wrap: anywhere; }
                @media print { body { padding: 0; } }
                @media (max-width: 600px) { .fields { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
            </style>
        </head>
        <body>
            <h1>Personal Information</h1>
            <div class="fields">${fieldsHtml}</div>
            <script>window.addEventListener('load', function () { window.focus(); window.print(); });<\/script>
        </body>
        </html>`);
    printWindow.document.close();
}
