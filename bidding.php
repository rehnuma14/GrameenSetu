<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <title>Grameen Setu – Live Bidding Hub | Seller & Bidder Portal</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- custom overrides for advanced bidding UI -->
  <style>
    * {
      transition: all 0.2s ease;
    }
    .top-nav .brand {
      font-size: 19px;
      font-weight: 800;
    }
    .bid-card {
      background: white;
      border-radius: 24px;
      padding: 1.2rem;
      border: 1px solid #e2f0e6;
      transition: all 0.25s;
      box-shadow: 0 4px 10px rgba(0,0,0,0.02);
    }
    .bid-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 30px -12px rgba(20, 83, 45, 0.2);
      border-color: #bbf7d0;
    }
    .role-toggle {
      background: #f0fdf4;
      border-radius: 100px;
      padding: 4px;
      display: inline-flex;
      gap: 8px;
      border: 1px solid #dcfce7;
    }
    .role-btn {
      padding: 6px 20px;
      border-radius: 40px;
      font-weight: 600;
      font-size: 0.85rem;
      cursor: pointer;
      background: transparent;
      color: #166534;
    }
    .role-btn.active {
      background: #14532d;
      color: white;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .bid-history-list {
      max-height: 160px;
      overflow-y: auto;
      background: #fefce8;
      border-radius: 16px;
      padding: 6px 10px;
      font-size: 0.75rem;
    }
    .badge-ended {
      background: #9ca3af;
      color: white;
      border-radius: 40px;
      padding: 2px 10px;
      font-size: 0.7rem;
      font-weight: 600;
    }
    .badge-active {
      background: #16a34a;
    }
    .flash-message {
      position: fixed;
      bottom: 24px;
      right: 24px;
      background: #1e293b;
      color: white;
      padding: 12px 24px;
      border-radius: 60px;
      z-index: 999;
      font-weight: 500;
      box-shadow: 0 10px 20px rgba(0,0,0,0.2);
      animation: fadeSlide 0.3s ease;
    }
    @keyframes fadeSlide {
      from { opacity: 0; transform: translateY(20px);}
      to { opacity: 1; transform: translateY(0);}
    }
    .modal-bg {
      background: rgba(0,0,0,0.5);
      backdrop-filter: blur(3px);
    }
    .counter-clock {
      font-family: monospace;
      font-weight: 700;
    }
  </style>
</head>
<body class="page-transition bg-gray-50">

<header class="top-nav">
  <div class="brand text-center" style="margin:0 auto;">
    <div style="font-size:30px;">🌾 Grameen Setu</div>
    <div style="color:#d1fae5; font-size:13px;">Farm to Business | Live Bidding</div>
  </div>
</header>

<div class="navigation">
  <ul>
    <li><a href="#"><span class="icon"></span><span class="title"><u>Grameen Setu</u></span></a></li>
    <li><a href="index.html"><span class="icon"><ion-icon name="home-outline"></ion-icon></span><span class="title">Dashboard</span></a></li>
    <li><a href="trending.html"><span class="icon"><ion-icon name="trending-up-outline"></ion-icon></span><span class="title">Trending</span></a></li>
    <li><a href="messages.html"><span class="icon"><ion-icon name="chatbubble-outline"></ion-icon></span><span class="title">Messages</span></a></li>
    <li class="hovered"><a href="bidding.html"><span class="icon"><ion-icon name="hammer-outline"></ion-icon></span><span class="title">Bidding</span></a></li>
    <li><a href="history.html"><span class="icon"><ion-icon name="time-outline"></ion-icon></span><span class="title">History</span></a></li>
    <li><a href="rating.html"><span class="icon"><ion-icon name="star-outline"></ion-icon></span><span class="title">Ratings</span></a></li>
    <li><a href="settings.html"><span class="icon"><ion-icon name="settings-outline"></ion-icon></span><span class="title">Settings</span></a></li>
    <li><a href="#" class="logout"><span class="icon"><ion-icon name="log-out-outline"></ion-icon></span><span class="title">Sign Out</span></a></li>
  </ul>
</div>

<div class="main">
  <div class="topbar">
    <div class="toggle"><ion-icon name="menu-outline"></ion-icon></div>
    <div class="search">
      <label>
        <input type="text" id="searchInput" placeholder="Search auctions by product...">
        <ion-icon name="search-outline"></ion-icon>
      </label>
    </div>
  </div>

  <div class="px-5 py-5">
    <!-- Role Toggle & Bidding statement header -->
    <div class="flex flex-col items-center mb-6 gap-3">
      <div class="page-title-wrapper m-0 text-center">
        <h2 class="text-2xl font-extrabold text-green-900">⚡ Live Bidding Arena</h2>
      </div>
      <div class="role-toggle shadow-sm">
        <button id="bidderRoleBtn" class="role-btn active">🤝 Bidder Mode</button>
        <button id="sellerRoleBtn" class="role-btn">📦 Seller Mode (Grameen Coop)</button>
      </div>
    </div>

    <!-- Seller specific: Create new auction (only visible in seller mode) -->
    <div id="sellerCreatePanel" class="max-w-4xl mx-auto mb-6 hidden">
      <div class="bg-white rounded-2xl p-4 border border-green-100 shadow-sm flex flex-wrap gap-3 items-center justify-between">
        <div class="font-semibold text-green-800 flex items-center gap-2"><ion-icon name="add-circle-outline" class="text-xl"></ion-icon> 🧑‍🌾 Seller Control: List New Auction</div>
        <button id="openCreateAuctionBtn" class="bg-green-700 hover:bg-green-800 text-white px-5 py-2 rounded-full text-sm font-bold shadow">+ Create Auction</button>
      </div>
    </div>

    <!-- Auction grid -->
    <div id="auctionsGrid" class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-6xl mx-auto">
      <!-- dynamic cards injected via JS -->
    </div>

    <!-- empty state message -->
    <div id="noAuctionsMsg" class="text-center text-gray-400 py-10 hidden">No auctions match your search or all auctions ended.</div>
  </div>
</div>

<!-- MODAL: Place Bid -->
<div id="bidModal" class="fixed inset-0 flex items-center justify-center z-50 hidden modal-bg transition-all" style="background: rgba(0,0,0,0.6);">
  <div class="bg-white rounded-2xl max-w-md w-full mx-4 p-6 shadow-2xl transform transition-all">
    <h3 class="text-xl font-bold text-green-800 flex items-center gap-2"><ion-icon name="gavel-outline"></ion-icon> Place Your Bid</h3>
    <div id="modalAuctionInfo" class="mt-2 text-sm text-gray-600"></div>
    <div class="mt-4">
      <label class="block text-sm font-semibold">Your Bid Amount (₹)</label>
      <input type="number" id="bidAmountInput" class="w-full border border-gray-300 rounded-xl px-4 py-2 mt-1 focus:ring-green-500 focus:border-green-500" step="50" min="0">
      <p class="text-xs text-gray-500 mt-1">💰 Minimum increment: ₹50 above current bid</p>
    </div>
    <div class="flex gap-3 mt-6">
      <button id="confirmBidBtn" class="bg-green-700 text-white px-5 py-2 rounded-xl font-bold flex-1">Confirm Bid</button>
      <button id="closeBidModal" class="bg-gray-200 text-gray-800 px-5 py-2 rounded-xl flex-1">Cancel</button>
    </div>
  </div>
</div>

<!-- MODAL: View Full Bid History (seller) -->
<div id="historyModal" class="fixed inset-0 flex items-center justify-center z-50 hidden modal-bg" style="background: rgba(0,0,0,0.6);">
  <div class="bg-white rounded-2xl max-w-lg w-full mx-4 p-6">
    <h3 class="text-xl font-bold text-green-800">📋 Complete Bid History</h3>
    <div id="historyModalList" class="mt-3 max-h-80 overflow-y-auto space-y-2"></div>
    <button id="closeHistoryModal" class="mt-5 bg-gray-200 w-full py-2 rounded-xl">Close</button>
  </div>
</div>

<!-- MODAL: Create Auction (seller) -->
<div id="createAuctionModal" class="fixed inset-0 flex items-center justify-center z-50 hidden modal-bg">
  <div class="bg-white rounded-2xl max-w-md w-full mx-4 p-6">
    <h3 class="text-xl font-bold text-green-800">🌱 Start New Auction</h3>
    <div class="mt-3 space-y-3">
      <input type="text" id="newProductName" placeholder="Product name (e.g., Organic Basmati)" class="w-full border rounded-xl px-4 py-2">
      <input type="text" id="newQuantity" placeholder="Quantity (e.g., 8 quintal)" class="w-full border rounded-xl px-4 py-2">
      <input type="number" id="newStartingBid" placeholder="Starting bid (₹)" class="w-full border rounded-xl px-4 py-2">
      <input type="number" id="newDurationMins" placeholder="Duration (minutes)" class="w-full border rounded-xl px-4 py-2" value="60">
    </div>
    <div class="flex gap-3 mt-5">
      <button id="confirmCreateAuction" class="bg-green-700 text-white px-4 py-2 rounded-xl flex-1">Create Auction</button>
      <button id="closeCreateModal" class="bg-gray-200 rounded-xl flex-1">Cancel</button>
    </div>
  </div>
</div>

<script src="assets/js/main.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
  // ---------- ADVANCED BIDDING SYSTEM (Seller + Bidder) ----------
  let currentRole = "bidder"; // 'bidder' or 'seller'
  const SELLER_IDENTITY = "Grameen Coop"; // seller's brand name
  let activeTimers = [];
  
  // Auction Data Model
  let auctions = [
    {
      id: "auc1",
      product: "🌾 Wheat (Premium)",
      quantity: "10 quintal",
      currentBid: 2300,
      startingBid: 2100,
      seller: "Grameen Coop",
      highestBidder: "AgriCorp",
      bids: [{ bidderName: "AgriCorp", amount: 2300, time: new Date(Date.now() - 3600000).toLocaleTimeString() },
              { bidderName: "FarmerMilton", amount: 2250, time: new Date(Date.now() - 7200000).toLocaleTimeString() }],
      endTime: new Date().getTime() + (4 * 3600000), // +4h
      status: "active",
      imageEmoji: "🌾"
    },
    {
      id: "auc2",
      product: "🍅 Tomato (Ripe Hybrid)",
      quantity: "5 quintal",
      currentBid: 1550,
      startingBid: 1400,
      seller: "Fresh Farms",
      highestBidder: "GreenMart",
      bids: [{ bidderName: "GreenMart", amount: 1550, time: new Date(Date.now() - 1800000).toLocaleTimeString() },
              { bidderName: "LocalRetail", amount: 1500, time: new Date(Date.now() - 5400000).toLocaleTimeString() }],
      endTime: new Date().getTime() + (1 * 3600000),
      status: "active",
      imageEmoji: "🍅"
    },
    {
      id: "auc3",
      product: "🧅 Onion (Red)",
      quantity: "8 quintal",
      currentBid: 1820,
      startingBid: 1700,
      seller: "Grameen Coop",
      highestBidder: "SpiceHub",
      bids: [{ bidderName: "SpiceHub", amount: 1820, time: new Date(Date.now() - 2500000).toLocaleTimeString() }],
      endTime: new Date().getTime() + (2.5 * 3600000),
      status: "active",
      imageEmoji: "🧅"
    },
    {
      id: "auc4",
      product: "🥔 Potato (Golden)",
      quantity: "12 quintal",
      currentBid: 2100,
      startingBid: 1950,
      seller: "Local Farmer Co",
      highestBidder: "SnackFoods",
      bids: [{ bidderName: "SnackFoods", amount: 2100, time: new Date(Date.now() - 4200000).toLocaleTimeString() },
              { bidderName: "VegChain", amount: 2050, time: new Date(Date.now() - 5000000).toLocaleTimeString() }],
      endTime: new Date().getTime() + (3 * 3600000),
      status: "active",
      imageEmoji: "🥔"
    }
  ];

  // Helper: update timers & check expiration
  function updateAllTimers() {
    const now = Date.now();
    let anyChange = false;
    auctions.forEach(auction => {
      if (auction.status === "active" && auction.endTime <= now) {
        auction.status = "ended";
        anyChange = true;
      }
    });
    if (anyChange) renderAuctionGrid();
  }

  function startGlobalTimer() {
    if (activeTimers.length) activeTimers.forEach(clearInterval);
    const interval = setInterval(() => {
      updateAllTimers();
      renderAuctionGrid(); // refresh timers display
    }, 1000);
    activeTimers.push(interval);
  }

  // format countdown
  function getCountdown(endTimeMs) {
    const diff = endTimeMs - Date.now();
    if (diff <= 0) return "Ended";
    const hours = Math.floor(diff / 3600000);
    const minutes = Math.floor((diff % 3600000) / 60000);
    const seconds = Math.floor((diff % 60000) / 1000);
    if (hours > 0) return `${hours}h ${minutes}m`;
    if (minutes > 0) return `${minutes}m ${seconds}s`;
    return `${seconds}s`;
  }

  // Flash message
  function flashMessage(msg, isError = false) {
    const existing = document.querySelector('.flash-message');
    if(existing) existing.remove();
    const div = document.createElement('div');
    div.className = 'flash-message';
    div.innerHTML = isError ? `⚠️ ${msg}` : `✅ ${msg}`;
    if(isError) div.style.background = '#b91c1c';
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
  }

  // Place bid logic
  function placeBid(auctionId, bidAmount) {
    const auction = auctions.find(a => a.id === auctionId);
    if (!auction) return false;
    if (auction.status !== "active") { flashMessage("Auction already ended!", true); return false; }
    if (Date.now() > auction.endTime) { auction.status = "ended"; renderAuctionGrid(); flashMessage("Auction closed.", true); return false; }
    const minBid = auction.currentBid + 50;
    if (bidAmount < minBid) {
      flashMessage(`Bid must be at least ₹${minBid} (current ₹${auction.currentBid} + ₹50 increment)`, true);
      return false;
    }
    // record bid
    const bidderName = currentRole === "bidder" ? "You (Bidder)" : "External Buyer";
    const newBid = {
      bidderName: bidderName,
      amount: bidAmount,
      time: new Date().toLocaleTimeString()
    };
    auction.bids.push(newBid);
    auction.currentBid = bidAmount;
    auction.highestBidder = bidderName;
    renderAuctionGrid();
    flashMessage(`🎉 Bid placed: ₹${bidAmount} on ${auction.product}`);
    return true;
  }

  // Seller actions: end auction
  function endAuctionBySeller(auctionId) {
    const auction = auctions.find(a => a.id === auctionId);
    if (!auction) return;
    if (auction.seller !== SELLER_IDENTITY) { flashMessage("You can only end your own auctions", true); return; }
    if (auction.status === "ended") { flashMessage("Already ended", true); return; }
    auction.status = "ended";
    renderAuctionGrid();
    flashMessage(`Auction for ${auction.product} has been closed.`);
  }

  // delete auction (seller)
  function deleteAuction(auctionId) {
    const auction = auctions.find(a => a.id === auctionId);
    if (!auction) return;
    if (auction.seller !== SELLER_IDENTITY) { flashMessage("Cannot delete others auction", true); return; }
    if (confirm(`Permanently delete auction "${auction.product}"?`)) {
      auctions = auctions.filter(a => a.id !== auctionId);
      renderAuctionGrid();
      flashMessage(`Auction removed.`);
    }
  }

  // Create new auction
  function createNewAuction(product, quantity, startingBid, durationMinutes) {
    if (!product.trim() || !quantity.trim() || isNaN(startingBid) || startingBid <= 0 || isNaN(durationMinutes) || durationMinutes <= 0) {
      flashMessage("Invalid fields! Fill product, quantity, starting bid >0, duration >0", true);
      return false;
    }
    const newId = "auc_" + Date.now() + "_" + Math.floor(Math.random()*1000);
    const startPrice = parseFloat(startingBid);
    const endTime = new Date().getTime() + (durationMinutes * 60 * 1000);
    const newAuction = {
      id: newId,
      product: product.trim(),
      quantity: quantity.trim(),
      currentBid: startPrice,
      startingBid: startPrice,
      seller: SELLER_IDENTITY,
      highestBidder: "No bids yet",
      bids: [],
      endTime: endTime,
      status: "active",
      imageEmoji: "🌱"
    };
    auctions.unshift(newAuction);
    renderAuctionGrid();
    flashMessage(`✅ New auction created: ${product} starting at ₹${startPrice}`);
    return true;
  }

  // show bid history modal (full list)
  function showBidHistory(auctionId) {
    const auction = auctions.find(a => a.id === auctionId);
    if (!auction) return;
    const modalList = document.getElementById("historyModalList");
    if (auction.bids.length === 0) {
      modalList.innerHTML = '<div class="text-gray-400 italic p-3">No bids placed yet.</div>';
    } else {
      modalList.innerHTML = auction.bids.slice().reverse().map(b => `
        <div class="bg-green-50 p-2 rounded-lg border-l-4 border-green-600">
          <div class="flex justify-between"><span class="font-semibold">${b.bidderName}</span><span class="text-green-800 font-bold">₹${b.amount}</span></div>
          <div class="text-xs text-gray-500">${b.time}</div>
        </div>
      `).join('');
    }
    document.getElementById("historyModal").classList.remove("hidden");
  }

  // render full grid with role-specific UI
  function renderAuctionGrid() {
    const grid = document.getElementById("auctionsGrid");
    const searchTerm = document.getElementById("searchInput")?.value.toLowerCase() || "";
    const filtered = auctions.filter(auction => auction.product.toLowerCase().includes(searchTerm));
    const noMsg = document.getElementById("noAuctionsMsg");
    if (filtered.length === 0) {
      grid.innerHTML = '';
      noMsg.classList.remove("hidden");
      return;
    }
    noMsg.classList.add("hidden");
    grid.innerHTML = filtered.map(auction => {
      const isSellerOwner = (currentRole === "seller" && auction.seller === SELLER_IDENTITY);
      const isBidderMode = (currentRole === "bidder");
      const countdownDisplay = auction.status === "active" ? getCountdown(auction.endTime) : "⛔ Ended";
      const endedBadge = auction.status !== "active" ? '<span class="badge-ended ml-2">Closed</span>' : '<span class="badge-active bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full">LIVE</span>';
      let recentBidsHtml = auction.bids.slice(-3).reverse().map(b => `<div class="flex justify-between text-xs"><span>${b.bidderName}</span><span class="font-mono">₹${b.amount}</span></div>`).join('');
      if (!recentBidsHtml) recentBidsHtml = '<div class="text-gray-400 text-xs">No bids yet</div>';
      
      // Bidder action button
      let actionButton = '';
      if (isBidderMode && auction.status === "active") {
        actionButton = `<button data-id="${auction.id}" class="trigger-bid w-full mt-3 bg-green-600 hover:bg-green-700 text-white py-2 rounded-full text-sm font-bold shadow-sm flex items-center justify-center gap-1"><ion-icon name="cash-outline"></ion-icon> Place Bid</button>`;
      } else if (isBidderMode && auction.status !== "active") {
        actionButton = `<button disabled class="w-full mt-3 bg-gray-300 text-gray-600 py-2 rounded-full text-sm font-bold cursor-not-allowed">Auction Ended</button>`;
      }
      
      // Seller controls
      let sellerControls = '';
      if (currentRole === "seller") {
        if (isSellerOwner) {
          sellerControls = `
            <div class="flex gap-2 mt-3">
              <button data-id="${auction.id}" class="view-history-seller flex-1 bg-amber-100 text-amber-800 py-1.5 rounded-full text-xs font-semibold hover:bg-amber-200"><ion-icon name="document-text-outline"></ion-icon> Bid History</button>
              ${auction.status === "active" ? `<button data-id="${auction.id}" class="end-auction-seller flex-1 bg-red-100 text-red-700 py-1.5 rounded-full text-xs font-semibold hover:bg-red-200"><ion-icon name="stop-circle-outline"></ion-icon> End</button>` : ''}
              <button data-id="${auction.id}" class="delete-auction-seller flex-1 bg-gray-200 text-gray-700 py-1.5 rounded-full text-xs font-semibold hover:bg-gray-300"><ion-icon name="trash-outline"></ion-icon> Delete</button>
            </div>
          `;
        } else {
          sellerControls = `<div class="mt-3 text-center text-xs text-gray-400 bg-gray-50 p-1 rounded-full">✖️ Not your listing (Seller: ${auction.seller})</div>`;
        }
      }
      
      return `
        <div class="bid-card" data-auction-id="${auction.id}">
          <div class="flex items-center gap-2 mb-1">
            <span class="text-3xl">${auction.imageEmoji || '🌾'}</span>
            <h3 class="font-bold text-lg text-green-900">${auction.product}</h3>
            ${endedBadge}
          </div>
          <p class="text-gray-600 text-sm">📦 ${auction.quantity} | 👨‍🌾 ${auction.seller}</p>
          <div class="mt-2 flex justify-between items-end">
            <div>
              <p class="text-gray-500 text-xs">💰 Current Bid</p>
              <p class="text-green-700 font-extrabold text-2xl">₹${auction.currentBid}</p>
              <p class="text-xs text-gray-500">🏆 ${auction.highestBidder}</p>
            </div>
            <div class="text-right">
              <p class="text-xs font-medium">⏳ ${countdownDisplay}</p>
              <p class="text-[11px] text-gray-400">Start: ₹${auction.startingBid}</p>
            </div>
          </div>
          <div class="mt-3 border-t pt-2">
            <p class="text-xs font-semibold text-gray-600 flex items-center gap-1"><ion-icon name="stats-chart-outline"></ion-icon> Recent bids (last 3)</p>
            <div class="bid-history-list mt-1">${recentBidsHtml || '<div class="text-gray-400 text-xs">—</div>'}</div>
          </div>
          ${actionButton}
          ${sellerControls}
        </div>
      `;
    }).join('');

    // attach event listeners dynamically
    document.querySelectorAll('.trigger-bid').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const auctionId = btn.getAttribute('data-id');
        const auction = auctions.find(a => a.id === auctionId);
        if (auction && currentRole === "bidder") {
          document.getElementById("modalAuctionInfo").innerHTML = `<strong>${auction.product}</strong> (${auction.quantity})<br>Current bid: ₹${auction.currentBid} | Min bid: ₹${auction.currentBid+50}`;
          const modal = document.getElementById("bidModal");
          modal.setAttribute("data-auction-id", auctionId);
          modal.classList.remove("hidden");
          document.getElementById("bidAmountInput").value = auction.currentBid + 50;
        } else {
          flashMessage("Only bidders can place bids", true);
        }
      });
    });
    // seller view history
    document.querySelectorAll('.view-history-seller').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const id = btn.getAttribute('data-id');
        showBidHistory(id);
      });
    });
    document.querySelectorAll('.end-auction-seller').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const id = btn.getAttribute('data-id');
        endAuctionBySeller(id);
      });
    });
    document.querySelectorAll('.delete-auction-seller').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const id = btn.getAttribute('data-id');
        deleteAuction(id);
      });
    });
  }

  // role switching UI
  function setRole(role) {
    currentRole = role;
    const bidderBtn = document.getElementById("bidderRoleBtn");
    const sellerBtn = document.getElementById("sellerRoleBtn");
    const sellerPanel = document.getElementById("sellerCreatePanel");
    if (role === "bidder") {
      bidderBtn.classList.add("active");
      sellerBtn.classList.remove("active");
      sellerPanel.classList.add("hidden");
    } else {
      sellerBtn.classList.add("active");
      bidderBtn.classList.remove("active");
      sellerPanel.classList.remove("hidden");
    }
    renderAuctionGrid();
  }

  // modal handlers
  function initModals() {
    const bidModal = document.getElementById("bidModal");
    const closeModal = document.getElementById("closeBidModal");
    const confirmBtn = document.getElementById("confirmBidBtn");
    closeModal.onclick = () => bidModal.classList.add("hidden");
    confirmBtn.onclick = () => {
      const auctionId = bidModal.getAttribute("data-auction-id");
      const amount = parseFloat(document.getElementById("bidAmountInput").value);
      if (isNaN(amount)) { flashMessage("Enter valid amount", true); return; }
      const success = placeBid(auctionId, amount);
      if (success) bidModal.classList.add("hidden");
      else bidModal.classList.add("hidden");
    };
    // history modal close
    document.getElementById("closeHistoryModal").onclick = () => document.getElementById("historyModal").classList.add("hidden");
    // create auction modal
    const createModal = document.getElementById("createAuctionModal");
    document.getElementById("openCreateAuctionBtn").onclick = () => createModal.classList.remove("hidden");
    document.getElementById("closeCreateModal").onclick = () => createModal.classList.add("hidden");
    document.getElementById("confirmCreateAuction").onclick = () => {
      const name = document.getElementById("newProductName").value;
      const qty = document.getElementById("newQuantity").value;
      const start = parseFloat(document.getElementById("newStartingBid").value);
      const mins = parseInt(document.getElementById("newDurationMins").value);
      if (createNewAuction(name, qty, start, mins)) {
        createModal.classList.add("hidden");
        document.getElementById("newProductName").value = "";
        document.getElementById("newQuantity").value = "";
        document.getElementById("newStartingBid").value = "";
      }
    };
    window.onclick = (e) => {
      if (e.target === bidModal) bidModal.classList.add("hidden");
      if (e.target === document.getElementById("historyModal")) document.getElementById("historyModal").classList.add("hidden");
      if (e.target === createModal) createModal.classList.add("hidden");
    };
  }

  // search filter
  function bindSearch() {
    const searchInput = document.getElementById("searchInput");
    searchInput.addEventListener("input", () => renderAuctionGrid());
  }

  // side menu toggle (from main.js fallback)
  document.querySelector('.toggle')?.addEventListener('click', function() {
    document.querySelector('.navigation')?.classList.toggle('active');
  });

  // initialze
  function init() {
    startGlobalTimer();
    bindSearch();
    initModals();
    document.getElementById("bidderRoleBtn").addEventListener("click", () => setRole("bidder"));
    document.getElementById("sellerRoleBtn").addEventListener("click", () => setRole("seller"));
    setRole("bidder");
    renderAuctionGrid();
  }
  init();
</script>
</body>
</html>