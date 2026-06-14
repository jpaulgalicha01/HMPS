function buildCategoryList(data) {
  const root = document.getElementById('categoryLevelList');
  root.innerHTML = '';

  if (!data || !data.data || data.data.length === 0) {
    root.innerHTML = `<li class="list-group-item text-muted">No categories found.</li>`;
    return;
  }

  for (const cat of data.data) {
    const catId = cat.CategoryID;
    const catName = cat.CategoryName;

    const catLi = document.createElement('li');
    catLi.className = 'list-group-item p-0 border-0 mb-1';
    catLi.style = "height: 250px";

    const header = document.createElement('button');
    header.type = 'button';
    header.className = 'list-group-item list-group-item-action d-flex align-items-center justify-content-between fw-semibold';
    header.setAttribute('data-category-id', catId);
    header.innerHTML = `<span>${escapeHtml(catName)}</span><span class="text-muted">Levels</span>`;

    const inner = document.createElement('ul');
    inner.className = 'list-group list-group-flush ms-3 mt-1';

    const levels = cat.CategoryListLevel || [];
    for (const lvl of levels) {
      const li = document.createElement('li');
      li.className = 'list-group-item d-flex align-items-center justify-content-between';

      const color = lvl.Color ?? '#0d6efd';
      li.innerHTML = `
        <div class="d-flex align-items-center gap-2">
          <span class="badge" style="background:${color}; color:#fff;">&nbsp;</span>
          <button type="button" class="btn btn-link p-0 text-start category-level-btn" 
            data-category-id="${catId}" 
            data-category-level-id="${lvl.CategoryLevelId ?? ''}" 
            data-category-level-name="${escapeHtml(lvl.CatLevelName)}">
            ${escapeHtml(lvl.CatLevelName)}
          </button>
        </div>
        <span class="text-muted small">View</span>
      `;

      inner.appendChild(li);
    }

    catLi.appendChild(header);
    catLi.appendChild(inner);
    root.appendChild(catLi);
  }
}

document.addEventListener('DOMContentLoaded', async () => {
  const tbody = document.getElementById('householdTableBody');
  fncExecute("inputConfig.php?getCategoryList=true",null,function(response){
    var res = JSON.parse(response);
    if(res.status == 200){
        buildCategoryList(res);
    }else{
        const root = document.getElementById('categoryLevelList');
        if (root) root.innerHTML = `<li class="list-group-item text-muted">Failed to load categories.</li>`;
    }
  },"GET")

  // Event delegation for level buttons
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.category-level-btn');
    if (!btn) return;
    const categoryId = btn.getAttribute('data-category-id');
    const categoryLevelId = btn.getAttribute('data-category-level-id');
    const categoryLevelName = btn.getAttribute('data-category-level-name');
    const header = document.getElementById('selectedCategoryLevelHeader');
    if (header) {
      header.textContent = categoryLevelName ? categoryLevelName : 'Selected category/level';
    }

    // If categoryLevelId missing due to current getCategoryList() response shape, we cannot filter.
    // For now we still request endpoint; UI will show empty list.
    // const url = `fetch-household-by-category-level.php?category_id=${encodeURIComponent(categoryId)}&category_level_id=${encodeURIComponent(categoryLevelId)}`;
    // try {
    //   const res = await fncGetJSON(url);
    //   renderHouseholds(res.data, tbody);
    // } catch (err) {
    //   renderHouseholds([], tbody);
    // }
    var formData = new FormData ();
    formData.append("category_id",encodeURIComponent(categoryId));
    formData.append("category_level_id",encodeURIComponent(categoryLevelId));
    formData.append("fetchingHouseHoldInfo",true);
    fncExecute("inputConfig.php", formData,function (response, textStatus, jqXHR){
        const res = JSON.parse(response);
        if(res.status == 200){
            renderHouseholds(res.data, tbody);
        }else{
            renderHouseholds([], tbody);
        }

    },"POST")


  });
});



function renderHouseholds(list, tbodyEl) {
  tbodyEl.innerHTML = '';
  if (!Array.isArray(list) || list.length === 0) {
    tbodyEl.innerHTML = `<tr><td colspan="3" class="text-center text-muted">No household found for this selection.</td></tr>`;
    return;
  }

  for (const row of list) {
    const householdNumber = escapeHtml(row.household_number ?? row.householdNumber ?? '');
    const familyMember = escapeHtml(row.FamilyMember ?? row.familyMember ?? '');
    const head = '';

    tbodyEl.insertAdjacentHTML(
      'beforeend',
      `<tr>
        <td class="text-nowrap">${householdNumber}</td>
        <td>${familyMember}</td>
        <td class="text-end">${head}</td>
      </tr>`
    );
  }
}