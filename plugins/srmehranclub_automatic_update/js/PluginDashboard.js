 class PluginDashboard {
            constructor() {
                this.currentQuery = ''; // Default search query
                this.currentPage = 1;
                this.totalResults = 0;
                this.totalPages = 0;
                this.isLoading = false;
                this.hasMoreResults = true;
                
                this.initializeElements();
                this.bindEvents();
                this.loadDefaultItems();
                this.downloadedItemList=srm_ajax?.downloadedItemList;
                this.ajax_url=srm_ajax?.ajax_url;
                this.plugins=srm_ajax?.plugins;
                this.themes=srm_ajax?.themes;
                this.isWhiteLebelEnable=srm_ajax?.isWhiteLebelEnable;


               

                // Make methods available globally for onclick handlers
                window.pluginDashboard = this;
            }

            initializeElements() {
                this.searchInput = document.getElementById('searchInput');
                this.searchBtn = document.getElementById('searchBtn');
                this.loadMoreBtn = document.getElementById('loadMoreBtn');
                this.tableBody = document.getElementById('tableBody');
                this.loadingContainer = document.getElementById('loadingContainer');
                this.noResultsContainer = document.getElementById('noResultsContainer');
                this.errorContainer = document.getElementById('errorContainer');
                this.statsContainer = document.getElementById('statsContainer');
            }

            bindEvents() {
                this.searchBtn.addEventListener('click', () => this.performSearch());
                this.loadMoreBtn.addEventListener('click', () => this.loadMoreResults());
                
                this.searchInput.addEventListener('keypress', (e) => {
                     const searchQuery = this.searchInput.value.trim();
                    if (e.key === 'Enter') {
                        this.performSearch();
                    }
                });

                this.searchInput.addEventListener('input', async (e) => {
                    const query = this.searchInput.value.trim();
                  
                    // When input is cleared
                    if (query == '') {
                        console.log("====>",query);
                        this.currentQuery = ''; // default fallback
                        this.currentPage = 1;
                       await this.loadDefaultItems(); // auto-load default results
                         console.log("====>",query);
                    }
                });
            }

            async loadDefaultItems() {
                // Load default items with 'seo' search
                this.searchInput.value = this.currentQuery;
                this.showLoading(true);
                this.hideContainers();

                try {
                    const results = await this.callSearchAPI(this.currentQuery, 1);
                    this.handleSearchResults(results, false);
                } catch (error) {
                    this.handleError(error);
                } finally {
                    this.showLoading(false);
                }
            }

            async performSearch(isLoadMore = false) {
                const query = this.searchInput.value.trim();
                
                if (!query && !isLoadMore) {
                    this.showError('Please enter a search query.');
                    return;
                }

                if (!isLoadMore) {
                    this.currentQuery = query;
                    this.currentPage = 1;
                    this.tableBody.innerHTML = '';
                    this.hasMoreResults = true;
                }

                this.showLoading(true);
                this.hideContainers();

                try {
                    const results = await this.callSearchAPI(this.currentQuery, this.currentPage);
                    this.handleSearchResults(results, isLoadMore);
                } catch (error) {
                    this.handleError(error);
                } finally {
                    this.showLoading(false);
                }
            }

            async callSearchAPI(query, page = 1) {
                const apiUrl = `https://database.srmehranclub.com/api/search/automatic-upgrade?search=${encodeURIComponent(query)}&page=${page}`;
                
                try {
                    const response = await fetch(apiUrl, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    return await response.json();
                } catch (error) {
                    throw new Error(`Failed to fetch data: ${error.message}`);
                }
            }

            handleSearchResults(data, isLoadMore) {
                if (!data.data || data.data.length === 0) {
                    if (!isLoadMore) {
                        this.showNoResults();
                    } else {
                        this.hasMoreResults = false;
                        this.updateLoadMoreButton();
                    }
                    return;
                }

                this.displayResults(data.data, isLoadMore);
                this.updateStats(data);
                this.hasMoreResults = this.currentPage < data.total_pages;
                this.updateLoadMoreButton();
            }

            displayResults(results, isLoadMore) {
                if (!isLoadMore) {
                    this.tableBody.innerHTML = '';
                }

                results.forEach((item, index) => {
                    const row = this.createTableRow(item);
                    this.tableBody.appendChild(row);
                    
                    // Add staggered entrance animation
                    setTimeout(() => {
                        row.style.opacity = '1';
                        row.style.transform = 'translateX(0)';
                    }, index * 50);
                });
            }

            createTableRow(item) {
                const row = document.createElement('tr');
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                row.style.transition = 'all 0.5s ease';
                
                const typeClass = `type-${item.prod_type || 'unknown'}`;
                const updatedDate = new Date(item.updated_at).toLocaleDateString();
                const title = this.decodeHtml(item.title);
                const developer = item.developer_name || 'Unknown';

                let isDownloaded = this.downloadedItemList.hasOwnProperty(title);
                let isInstalled = this.getInstalledProductByName(title, item.prod_type);
                console.log("isInstalled", isInstalled);
                
                let conditionalButtons = ``;
                let ReInstallButtons = ``;
                if (!isInstalled && isDownloaded) {
                     conditionalButtons = `<button class="btn btn-primary activate-btn" data-item-id="${item.id}" onclick="window.pluginDashboard.activateItem(${item.id}, this)">Activate</button>`;
                } else if(isDownloaded && isInstalled){
                        conditionalButtons = `<button class="btn btn-primary">Activated</button>`;
                         ReInstallButtons = `<button class="btn btn-success download-btn" data-item-id="${item.id}" onclick="window.pluginDashboard.downloadItem(${item.id}, this)">Re Install</button>`;
                }else{
                     conditionalButtons = `<button class="btn btn-success download-btn" data-item-id="${item.id}" onclick="window.pluginDashboard.downloadItem(${item.id}, this)">Install</button>`;
                } 

                let livebutton=``;
                    if(!this.isWhiteLebelEnable){

                       livebutton=`<a href="${item.live_link}" target="_blank" class="btn btn-primary">Details</a>`;
                    }

                row.innerHTML = `
                    <td class="plugin-name">${this.escapeHtml(title)}</td>
                    <td><span class="plugin-type ${typeClass}">${item.prod_type || 'N/A'}</span></td>
                    <td class="developer">${this.escapeHtml(developer)}</td>
                    <td><span class="version">${this.escapeHtml(item.prod_version || 'N/A')}</span></td>
                    <td class="updated-date">${updatedDate}</td>
                    <td class="options">
                            ${livebutton}
                        ${conditionalButtons}
                        ${ReInstallButtons}
                    </td>
                `;

                return row;
            }

            updateStats(data) {
                if (data.total_products) {
                    const currentTotal = this.tableBody.children.length;
                   this.statsContainer.innerHTML = `
                        Showing ${currentTotal} of ${data.total_products}
                        ${this.currentQuery ? `results for <strong>${this.escapeHtml(this.currentQuery)}</strong>` : ""}
                        (Page ${this.currentPage} of ${data.total_pages})
                    `;
                    this.statsContainer.style.display = 'block';
                    
                    // Update pagination info
                    this.totalResults = data.total_products;
                    this.totalPages = data.total_pages;
                }
            }

            updateLoadMoreButton() {
                if (this.hasMoreResults && this.tableBody.children.length > 0) {
                    this.loadMoreBtn.style.display = 'block';
                    this.loadMoreBtn.textContent = `Load More (${this.currentPage}/${this.totalPages})`;
                } else if (this.tableBody.children.length > 0) {
                    this.loadMoreBtn.style.display = 'none';
                    // Show end of results message
                    if (!document.getElementById('endMessage')) {
                        const endMsg = document.createElement('div');
                        endMsg.id = 'endMessage';
                        endMsg.className = 'no-results';
                        endMsg.innerHTML = `
                            <div style="font-size: 2em; margin-bottom: 10px;">✨</div>
                            <div>You've reached the end of search results!</div>
                            <div style="margin-top: 10px; font-size: 0.9em; color: #999;">
                                Found ${this.totalResults} total results
                            </div>
                        `;
                        this.loadMoreBtn.parentNode.insertBefore(endMsg, this.loadMoreBtn.nextSibling);
                    }
                } else {
                    this.loadMoreBtn.style.display = 'none';
                }
            }

            async loadMoreResults() {
                if (this.isLoading || !this.hasMoreResults) return;
                
                this.currentPage++;
                await this.performSearch(true);
            }

            showLoading(show) {
                this.isLoading = show;
                this.loadingContainer.style.display = show ? 'block' : 'none';
                this.searchBtn.disabled = show;
                this.loadMoreBtn.disabled = show;
                
                if (show) {
                    this.searchBtn.textContent = 'Searching...';
                } else {
                    this.searchBtn.textContent = 'Search';
                }
            }

            showNoResults() {
                this.noResultsContainer.style.display = 'block';
                this.loadMoreBtn.style.display = 'none';
                this.statsContainer.style.display = 'none';
            }

            showError(message) {
                this.errorContainer.textContent = message;
                this.errorContainer.style.display = 'block';
                setTimeout(() => {
                    this.errorContainer.style.display = 'none';
                }, 5000);
            }

            handleError(error) {
                console.error('Search error:', error);
                this.showError(`Search failed: ${error.message || 'Please try again later.'}`);
                this.loadMoreBtn.style.display = 'none';
            }

            hideContainers() {
                this.noResultsContainer.style.display = 'none';
                this.errorContainer.style.display = 'none';
                
                // Remove end message if exists
                const endMessage = document.getElementById('endMessage');
                if (endMessage) {
                    endMessage.remove();
                }
            }

            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            decodeHtml(html) {
                const txt = document.createElement('textarea');
                txt.innerHTML = html;
                return txt.value;
            }

            downloadItem(id, buttonElement = null) {
                // Get the button element (either passed or from event)
                const button = buttonElement || event.target;
                
                // Disable button during request
                button.disabled = true;
                button.innerHTML = 'Installing...';
                
                // WordPress AJAX request
                jQuery.ajax({
                    url:this.ajax_url, // WordPress AJAX URL
                    type: 'POST',
                    data: {
                        action: 'download_item', // Your WordPress action hook
                        item_id: id,
                    },
                    success: (response) => {
                        if (response.success) {
                            // Change button text and function on success
                            button.innerHTML = 'Activate';
                            button.className = 'btn btn-primary activate-btn';
                            button.onclick = () => this.activateItem(id, button);
                            button.disabled = false;
                            Swal.fire({
                              title: "Success!",
                              text:response?.data,
                              icon: "success"
                            });
                        } else {
                            // Handle error
                            button.innerHTML = 'Install Failed';
                            button.className = 'btn btn-danger';
                            button.disabled = false;
                              Swal.fire({
                              title: "Error!",
                              text:'This product may not include a separate installation file, so the installation may not work as expected. Please download the product from our website and complete the installation manually. This issue will be resolved in the next update.',
                              icon: "error"
                            });
                            
                        }
                    },
                    error: (xhr, status, error) => {
                        // Handle AJAX error
                        button.innerHTML = 'Install Error';
                        button.className = 'btn btn-danger';
                        button.disabled = false;
                        console.error('AJAX error:', error);
                    }
                });
            }

            activateItem(id, buttonElement = null) {
                // Get the button element (either passed or from event)
                const button = buttonElement || event.target;
                
                // Disable button during request
                button.disabled = true;
                button.innerHTML = 'Activating...';
                
                // WordPress AJAX request for activation
                jQuery.ajax({
                    url: this.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'activate_item',
                        item_id: id
                    },
                    success: (response) => {
                        if (response.success) {
                            button.innerHTML = 'Activated';
                            button.className = 'btn btn-success';
                            button.disabled = true; // Keep disabled as item is now activated
                            button.onclick = null; // Remove onclick handler
                              Swal.fire({
                              title: "Success!",
                              text:response?.data?.message,
                              icon: "success"
                            });
                        } else {
                            button.innerHTML = 'Activation Failed';
                            button.className = 'btn btn-danger';
                            button.disabled = false;
                            Swal.fire({
                              title: "Error!",
                              text:'This product may not include a separate installation file, so the installation may not work as expected. Please download the product from our website and complete the installation manually. This issue will be resolved in the next update.',
                              icon: "error"
                            });
                        }
                    },
                    error: (xhr, status, error) => {
                        button.innerHTML = 'Activation Error';
                        button.className = 'btn btn-danger';
                        button.disabled = false;
                        console.error('AJAX error:', error);
                    }
                });
            }

            getInstalledProductByName(name, type) {
                // Clean the search term and create a more flexible regex pattern
                let cleanName = name.toLowerCase().trim();
                
                // Split into words and create a pattern that allows for partial matches
                let words = cleanName.split(/\s+/).filter(word => word.length > 0);
                
                // Create regex patterns for different matching strategies
                let exactPattern = cleanName.replace(/[^a-z0-9\s]/gi, "").replace(/\s+/g, "\\s*");
                let flexiblePattern = words.map(word => 
                    word.replace(/[^a-z0-9]/gi, "")
                ).join(".*");
                
                let plugins = this.plugins;
                let themes = this.themes;
                
                const searchIn = type === "plugin" ? plugins : themes;
                
                if (!searchIn) {
                    return false;
                }
                
                // Strategy 1: Try exact name match first
                for (let idx in searchIn) {
                    let item = searchIn[idx];
                    if (!item?.Name) continue;
                    
                    let itemName = item.Name.toLowerCase();
                    
                    // Exact match
                    if (itemName === cleanName) {
                        item.file = idx;
                        console.log("Exact match found:", item.Name);
                        return item;
                    }
                }
                
                // Strategy 2: Try contains match
                for (let idx in searchIn) {
                    let item = searchIn[idx];
                    if (!item?.Name) continue;
                    
                    let itemName = item.Name.toLowerCase();
                    
                    // Check if all words from search are present in the item name
                    let allWordsPresent = words.every(word => itemName.includes(word));
                    
                    if (allWordsPresent) {
                        item.file = idx;
                        console.log("Word-based match found:", item.Name);
                        return item;
                    }
                }
                
                // Strategy 3: Try partial match (any word matches)
                for (let idx in searchIn) {
                    let item = searchIn[idx];
                    if (!item?.Name) continue;
                    
                    let itemName = item.Name.toLowerCase();
                    
                    // Check if any word from search is present in the item name
                    let anyWordPresent = words.some(word => itemName.includes(word));
                    
                    if (anyWordPresent) {
                        item.file = idx;
                        console.log("Partial match found:", item.Name);
                        return item;
                    }
                }
                
                // Strategy 4: Try flexible regex as fallback
                try {
                    let flexibleRegex = new RegExp(flexiblePattern, "i");
                    
                    for (let idx in searchIn) {
                        let item = searchIn[idx];
                        if (!item?.Name) continue;
                        
                        if (flexibleRegex.test(item.Name.toLowerCase())) {
                            item.file = idx;
                            console.log("Regex match found:", item.Name);
                            return item;
                        }
                    }
                } catch (e) {
                    console.log("Regex error:", e.message);
                }
                
                console.log("No match found for:", name);
                return false;
            }
        }