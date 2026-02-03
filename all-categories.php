<?php
require_once __DIR__ . '/admin/layouts/database.php';

// Ambil semua parent categories (parent_id = NULL atau 0)
$parentCategories = $db->query("
    SELECT c.*, COUNT(DISTINCT p.id) as product_count
    FROM categories c
    LEFT JOIN products p ON c.id = p.category_id AND p.status = 'active'
    WHERE c.status = 'active' AND (c.parent_id IS NULL OR c.parent_id = 0)
    GROUP BY c.id
    ORDER BY c.name ASC
")->fetchAll();

// Ambil semua child categories
$childCategories = $db->query("
    SELECT c.*, c.parent_id, COUNT(DISTINCT p.id) as product_count
    FROM categories c
    LEFT JOIN products p ON c.id = p.category_id AND p.status = 'active'
    WHERE c.status = 'active' AND c.parent_id IS NOT NULL AND c.parent_id > 0
    GROUP BY c.id
    ORDER BY c.name ASC
")->fetchAll();

// Organize children by parent
$childrenByParent = [];
foreach ($childCategories as $child) {
    $childrenByParent[$child['parent_id']][] = $child;
}

include 'includes/header.php';
?>

<style>
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 80px 0;
    color: white;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" fill="white" opacity="0.05"/></svg>');
    background-size: 100px 100px;
}

.page-header > * {
    position: relative;
    z-index: 1;
}

.page-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 15px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.page-subtitle {
    font-size: 1.2rem;
    opacity: 0.95;
    max-width: 600px;
    margin: 0 auto;
}

.categories-section {
    padding: 60px 0;
    background: #f8f9fa;
}

/* Search & Filter */
.search-filter-section {
    background: white;
    padding: 30px 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.search-box {
    max-width: 500px;
    margin: 0 auto;
    position: relative;
}

.search-box input {
    width: 100%;
    padding: 15px 50px 15px 20px;
    border: 2px solid #e0e0e0;
    border-radius: 50px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.search-box input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.search-icon {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
}

/* Parent Category Section */
.parent-category-section {
    margin-bottom: 50px;
}

.parent-category-header {
    background: white;
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 25px;
    transition: all 0.3s ease;
}

.parent-category-header:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

.parent-icon-container {
    flex-shrink: 0;
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 15px;
}

.parent-icon-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    opacity: 0.8;
}

.parent-info {
    flex-grow: 1;
}

.parent-name {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.parent-name a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.parent-name a:hover {
    color: #667eea;
}

.parent-description {
    color: #666;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 10px;
}

.parent-stats {
    display: flex;
    gap: 25px;
    margin-top: 12px;
}

.stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f8f9fa;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #667eea;
}

.stat-badge svg {
    width: 16px;
    height: 16px;
}

.expand-toggle {
    flex-shrink: 0;
    background: #667eea;
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.expand-toggle:hover {
    background: #764ba2;
    transform: scale(1.1);
}

.expand-toggle.expanded {
    transform: rotate(180deg);
}

/* Child Categories Grid */
.child-categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    padding-left: 40px;
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transition: all 0.5s ease;
}

.child-categories-grid.show {
    max-height: 2000px;
    opacity: 1;
    margin-bottom: 20px;
}

.child-category-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    text-decoration: none;
    display: block;
    border-left: 4px solid #667eea;
}

.child-category-card:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.child-icon-small {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8px;
    margin-bottom: 15px;
}

.child-icon-small img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    opacity: 0.7;
}

.child-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.child-count {
    color: #667eea;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* No Results */
.no-results {
    text-align: center;
    padding: 80px 20px;
    display: none;
}

.no-results img {
    max-width: 300px;
    margin-bottom: 30px;
    opacity: 0.5;
}

.no-results h3 {
    font-size: 1.5rem;
    color: #666;
    margin-bottom: 15px;
}

.no-results p {
    color: #999;
}

/* Responsive */
@media (max-width: 992px) {
    .parent-category-header {
        flex-direction: column;
        text-align: center;
    }
    
    .parent-stats {
        justify-content: center;
    }
    
    .child-categories-grid {
        padding-left: 0;
    }
}

@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
    }
    
    .parent-name {
        font-size: 1.4rem;
    }
    
    .child-categories-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }
}

@media (max-width: 576px) {
    .child-categories-grid {
        grid-template-columns: 1fr;
    }
    
    .parent-stats {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
}
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Product Categories</h1>
        <p class="page-subtitle">Find the perfect packaging solution for your specific needs</p>
    </div>
</section>

<!-- Search & Filter -->
<section class="search-filter-section">
    <div class="container">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search categories..." onkeyup="searchCategories()">
            <svg class="search-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
            </svg>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
    <div class="container">
        <div id="categoriesContainer">
            <?php foreach ($parentCategories as $parent): 
                $icon = (!empty($parent['icon']) && file_exists("admin/uploads/categories/" . $parent['icon']))
                    ? "admin/uploads/categories/" . $parent['icon']
                    : "https://via.placeholder.com/100x100?text=" . urlencode(substr($parent['name'], 0, 1));
                
                $hasChildren = isset($childrenByParent[$parent['id']]) && count($childrenByParent[$parent['id']]) > 0;
                $totalProducts = $parent['product_count'];
                
                // Calculate total products including children
                if ($hasChildren) {
                    foreach ($childrenByParent[$parent['id']] as $child) {
                        $totalProducts += $child['product_count'];
                    }
                }
            ?>
            <div class="parent-category-section" data-search="<?= strtolower($parent['name']) ?>">
                <div class="parent-category-header">
                    <div class="parent-icon-container">
                        <img src="<?= $icon ?>" alt="<?= htmlspecialchars($parent['name']) ?>">
                    </div>
                    
                    <div class="parent-info">
                        <h2 class="parent-name">
                            <a href="categories.php?id=<?= $parent['id'] ?>">
                                <?= htmlspecialchars($parent['name']) ?>
                            </a>
                        </h2>
                        
                        <?php if (!empty($parent['description'])): ?>
                        <p class="parent-description"><?= htmlspecialchars($parent['description']) ?></p>
                        <?php endif; ?>
                        
                        <div class="parent-stats">
                            <span class="stat-badge">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                </svg>
                                <?= $totalProducts ?> Products
                            </span>
                            
                            <?php if ($hasChildren): ?>
                            <span class="stat-badge">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                                </svg>
                                <?= count($childrenByParent[$parent['id']]) ?> Subcategories
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($hasChildren): ?>
                    <button class="expand-toggle" onclick="toggleChildren(this, <?= $parent['id'] ?>)" aria-label="Expand subcategories">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <?php endif; ?>
                </div>
                
                <?php if ($hasChildren): ?>
                <div class="child-categories-grid" id="children-<?= $parent['id'] ?>">
                    <?php foreach ($childrenByParent[$parent['id']] as $child): 
                        $childIcon = (!empty($child['icon']) && file_exists("admin/uploads/categories/" . $child['icon']))
                            ? "admin/uploads/categories/" . $child['icon']
                            : "https://via.placeholder.com/50x50?text=" . urlencode(substr($child['name'], 0, 1));
                    ?>
                        <a href="categories.php?id=<?= $child['id'] ?>" class="child-category-card">
                            <div class="child-icon-small">
                                <img src="<?= $childIcon ?>" alt="<?= htmlspecialchars($child['name']) ?>">
                            </div>
                            <h3 class="child-name"><?= htmlspecialchars($child['name']) ?></h3>
                            <p class="child-count">
                                <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                </svg>
                                <?= $child['product_count'] ?> Products
                            </p>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="no-results" id="noResults">
            <img src="https://via.placeholder.com/300x300?text=No+Results" alt="No Results">
            <h3>No categories found</h3>
            <p>Try a different search term</p>
        </div>
    </div>
</section>

<script>
// Toggle children categories
function toggleChildren(button, parentId) {
    const childrenGrid = document.getElementById('children-' + parentId);
    const isExpanded = childrenGrid.classList.contains('show');
    
    if (isExpanded) {
        childrenGrid.classList.remove('show');
        button.classList.remove('expanded');
    } else {
        childrenGrid.classList.add('show');
        button.classList.add('expanded');
    }
}

// Search categories
function searchCategories() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const parentSections = document.querySelectorAll('.parent-category-section');
    const noResults = document.getElementById('noResults');
    let foundCount = 0;
    
    parentSections.forEach(section => {
        const searchText = section.getAttribute('data-search');
        const childCards = section.querySelectorAll('.child-category-card');
        let parentMatch = searchText.includes(searchInput);
        let childMatch = false;
        
        // Check child categories
        childCards.forEach(card => {
            const childName = card.querySelector('.child-name').textContent.toLowerCase();
            if (childName.includes(searchInput)) {
                childMatch = true;
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide parent section
        if (parentMatch || childMatch || searchInput === '') {
            section.style.display = 'block';
            foundCount++;
            
            // Auto-expand if child matches
            if (childMatch && searchInput !== '') {
                const childrenGrid = section.querySelector('.child-categories-grid');
                const expandButton = section.querySelector('.expand-toggle');
                if (childrenGrid && expandButton) {
                    childrenGrid.classList.add('show');
                    expandButton.classList.add('expanded');
                }
            }
            
            // Reset child display if searching parent or empty
            if (parentMatch || searchInput === '') {
                childCards.forEach(card => {
                    card.style.display = 'block';
                });
            }
        } else {
            section.style.display = 'none';
        }
    });
    
    // Show/hide no results message
    if (foundCount === 0) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}

// Expand all on page load (optional)
// document.addEventListener('DOMContentLoaded', function() {
//     document.querySelectorAll('.expand-toggle').forEach(button => {
//         button.click();
//     });
// });
</script>

<?php include 'includes/footer.php'; ?>