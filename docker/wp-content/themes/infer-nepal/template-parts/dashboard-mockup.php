<svg class="dash-svg" viewBox="0 0 1200 510" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
  <defs>
    <linearGradient id="hdrG" x1="0" x2="1"><stop offset="0" stop-color="#0A7AA9"/><stop offset="1" stop-color="#1f4d68"/></linearGradient>
    <linearGradient id="bar1" x1="0" x2="0" y2="1"><stop offset="0" stop-color="#0A7AA9" stop-opacity=".95"/><stop offset="1" stop-color="#0A7AA9" stop-opacity=".55"/></linearGradient>
  </defs>
  <rect width="1200" height="510" fill="#f6f8fa"/>
  <rect x="0" y="0" width="1200" height="44" fill="url(#hdrG)"/>
  <text x="22" y="28" fill="#fff" font-family="Montserrat" font-weight="700" font-size="14"><?= esc_html(($sw['title'] ?? 'Software') . ' · Sample Co. · Books FY 2082-83') ?></text>
  <circle cx="1130" cy="22" r="10" fill="#ffffff" opacity=".18"/>
  <circle cx="1160" cy="22" r="10" fill="#ffffff" opacity=".18"/>
  <rect x="0" y="44" width="170" height="466" fill="#1f2a36"/>
  <g font-family="Montserrat" font-size="12" fill="#9bb0c5">
    <rect x="12" y="64" width="146" height="30" rx="6" fill="#0A7AA9"/>
    <text x="28" y="83" fill="#fff" font-weight="600">Dashboard</text>
    <text x="28" y="118">Vouchers</text>
    <text x="28" y="148">Day Book</text>
    <text x="28" y="178">Inventory</text>
    <text x="28" y="208">VAT Returns</text>
    <text x="28" y="238">Payroll</text>
    <text x="28" y="268">Banking</text>
    <text x="28" y="298">Reports</text>
    <text x="28" y="328">Reconciliation</text>
    <text x="28" y="358">Multi-Godown</text>
  </g>
  <g font-family="Montserrat">
    <g transform="translate(196,68)">
      <rect width="220" height="86" rx="10" fill="#fff" stroke="#e3e8ee"/>
      <text x="14" y="22" font-size="10" fill="#8a96a3" font-weight="700" letter-spacing="1">SALES (MTD)</text>
      <text x="14" y="52" font-size="22" font-weight="800" fill="#1c2632">रु 84,32,510</text>
      <text x="14" y="72" font-size="11" fill="#0f9d58" font-weight="600">▲ 12.4% vs last month</text>
    </g>
    <g transform="translate(428,68)">
      <rect width="220" height="86" rx="10" fill="#fff" stroke="#e3e8ee"/>
      <text x="14" y="22" font-size="10" fill="#8a96a3" font-weight="700" letter-spacing="1">RECEIVABLES</text>
      <text x="14" y="52" font-size="22" font-weight="800" fill="#1c2632">रु 12,18,400</text>
      <text x="14" y="72" font-size="11" fill="#dc2626" font-weight="600">▲ 8 invoices overdue</text>
    </g>
    <g transform="translate(660,68)">
      <rect width="220" height="86" rx="10" fill="#fff" stroke="#e3e8ee"/>
      <text x="14" y="22" font-size="10" fill="#8a96a3" font-weight="700" letter-spacing="1">CASH IN HAND</text>
      <text x="14" y="52" font-size="22" font-weight="800" fill="#1c2632">रु 4,55,200</text>
      <text x="14" y="72" font-size="11" fill="#5a6776" font-weight="600">3 banks · last sync 2m</text>
    </g>
    <g transform="translate(892,68)">
      <rect width="290" height="86" rx="10" fill="#0A7AA9"/>
      <text x="14" y="22" font-size="10" fill="#cdeafa" font-weight="700" letter-spacing="1">VAT PAYABLE (THIS Q)</text>
      <text x="14" y="52" font-size="22" font-weight="800" fill="#fff">रु 1,42,860</text>
      <text x="14" y="72" font-size="11" fill="#cdeafa" font-weight="600">Due in 9 days · File now →</text>
    </g>
  </g>
  <g transform="translate(196,178)" font-family="Montserrat">
    <rect width="684" height="240" rx="10" fill="#fff" stroke="#e3e8ee"/>
    <text x="20" y="28" font-size="13" font-weight="700" fill="#1c2632">Cash flow · last 8 weeks</text>
    <text x="20" y="46" font-size="11" fill="#8a96a3">Net inflow vs outflow per ISO week</text>
    <g transform="translate(28,72)">
      <line x1="0" x2="640" y1="135" y2="135" stroke="#e3e8ee"/>
      <line x1="0" x2="640" y1="100" y2="100" stroke="#eef1f4" stroke-dasharray="3 3"/>
      <line x1="0" x2="640" y1="65" y2="65" stroke="#eef1f4" stroke-dasharray="3 3"/>
      <line x1="0" x2="640" y1="30" y2="30" stroke="#eef1f4" stroke-dasharray="3 3"/>
      <g>
        <rect x="10" y="58" width="32" height="77" rx="3" fill="url(#bar1)"/>
        <rect x="50" y="40" width="32" height="95" rx="3" fill="url(#bar1)"/>
        <rect x="90" y="62" width="32" height="73" rx="3" fill="url(#bar1)"/>
        <rect x="130" y="22" width="32" height="113" rx="3" fill="url(#bar1)"/>
        <rect x="170" y="8" width="32" height="127" rx="3" fill="url(#bar1)"/>
        <rect x="210" y="48" width="32" height="87" rx="3" fill="url(#bar1)"/>
        <rect x="250" y="30" width="32" height="105" rx="3" fill="url(#bar1)"/>
        <rect x="290" y="44" width="32" height="91" rx="3" fill="url(#bar1)"/>
        <polyline points="26,112 66,98 106,124 146,76 186,60 226,90 266,84 306,72"
                  fill="none" stroke="#dc2626" stroke-width="2.5"/>
        <g font-size="10" fill="#5a6776">
          <text x="20" y="150">W31</text><text x="60" y="150">W32</text><text x="100" y="150">W33</text>
          <text x="140" y="150">W34</text><text x="180" y="150">W35</text><text x="220" y="150">W36</text>
          <text x="260" y="150">W37</text><text x="300" y="150">W38</text>
        </g>
      </g>
    </g>
  </g>
  <g transform="translate(892,178)" font-family="Montserrat">
    <rect width="290" height="240" rx="10" fill="#fff" stroke="#e3e8ee"/>
    <text x="14" y="28" font-size="13" font-weight="700" fill="#1c2632">Top selling items</text>
    <g font-size="11" fill="#5a6776">
      <text x="14" y="56">Basmati Rice 25kg</text>
      <rect x="14" y="64" width="262" height="6" rx="3" fill="#eef1f4"/>
      <rect x="14" y="64" width="232" height="6" rx="3" fill="#0A7AA9"/>
      <text x="14" y="92">Mustard Oil 1L</text>
      <rect x="14" y="100" width="262" height="6" rx="3" fill="#eef1f4"/>
      <rect x="14" y="100" width="190" height="6" rx="3" fill="#0A7AA9"/>
      <text x="14" y="128">Wai-Wai Carton</text>
      <rect x="14" y="136" width="262" height="6" rx="3" fill="#eef1f4"/>
      <rect x="14" y="136" width="158" height="6" rx="3" fill="#0A7AA9"/>
      <text x="14" y="164">Sugar 50kg</text>
      <rect x="14" y="172" width="262" height="6" rx="3" fill="#eef1f4"/>
      <rect x="14" y="172" width="120" height="6" rx="3" fill="#0A7AA9"/>
    </g>
  </g>
</svg>
