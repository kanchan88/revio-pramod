/**
 * Infer Nepal — Editor-side block registrations.
 *
 * Each block is server-side rendered (PHP render_callback). This file:
 *   - registers them in the block inserter
 *   - wires Inspector controls (sidebar dropdowns / sliders)
 *   - shows a live ServerSideRender preview while editing
 *
 * No build step — uses wp.element.createElement (no JSX).
 */
(function (wp) {
  if (!wp || !wp.blocks) return;

  var el                = wp.element.createElement;
  var registerBlockType = wp.blocks.registerBlockType;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var PanelBody         = wp.components.PanelBody;
  var SelectControl     = wp.components.SelectControl;
  var RangeControl      = wp.components.RangeControl;
  var TextControl       = wp.components.TextControl;
  var ToggleControl     = wp.components.ToggleControl;
  var ServerSideRender  = wp.serverSideRender || wp.editor.ServerSideRender;

  var data = window.inpBlockData || { industries: [{label: '— Any industry —', value: ''}],
                                      categories: [{label: '— Any category —', value: ''}] };

  /* ============================================================
   * 1) Software list (cards or rows, filterable)
   * ============================================================ */
  registerBlockType('infer-nepal/software-list', {
    title:       'Software list (live)',
    description: 'Auto-populated list of software, optionally filtered by industry or category.',
    icon:        'list-view',
    category:    'infer-nepal',
    keywords:    ['software', 'list', 'directory', 'category'],
    supports:    { html: false, align: ['wide', 'full'] },
    attributes:  {
      industry:       { type: 'string',  default: '' },
      category:       { type: 'string',  default: '' },
      count:          { type: 'number',  default: 6 },
      orderby:        { type: 'string',  default: 'rating' },
      display:        { type: 'string',  default: 'cards' },
      featured_first: { type: 'boolean', default: true }
    },
    edit: function (props) {
      var a = props.attributes;
      var set = props.setAttributes;
      return el('div', { className: props.className },
        el(InspectorControls, {},
          el(PanelBody, { title: 'Software list settings', initialOpen: true },
            el(SelectControl, {
              label: 'Industry',
              value: a.industry,
              options: data.industries,
              onChange: function (v) { set({ industry: v }); }
            }),
            el(SelectControl, {
              label: 'Category',
              value: a.category,
              options: data.categories,
              onChange: function (v) { set({ category: v }); }
            }),
            el(SelectControl, {
              label: 'Sort by',
              value: a.orderby,
              options: [
                { label: 'Rating',  value: 'rating'  },
                { label: 'Reviews', value: 'reviews' },
                { label: 'Newest',  value: 'date'    }
              ],
              onChange: function (v) { set({ orderby: v }); }
            }),
            el(SelectControl, {
              label: 'Display as',
              value: a.display,
              options: [
                { label: 'Cards (3-col grid)', value: 'cards' },
                { label: 'Rows (vertical list)', value: 'rows' }
              ],
              onChange: function (v) { set({ display: v }); }
            }),
            el(RangeControl, {
              label: 'How many',
              value: a.count,
              min: 1, max: 24, step: 1,
              onChange: function (v) { set({ count: v }); }
            }),
            a.display === 'rows' && el(ToggleControl, {
              label: 'Highlight first as "Top listing"',
              checked: a.featured_first,
              onChange: function (v) { set({ featured_first: v }); }
            })
          )
        ),
        ServerSideRender
          ? el(ServerSideRender, { block: 'infer-nepal/software-list', attributes: a })
          : el('div', {}, 'Software list — preview unavailable')
      );
    },
    save: function () { return null; } // server-rendered
  });

  /* ============================================================
   * 2) Industry tile grid
   * ============================================================ */
  registerBlockType('infer-nepal/industry-grid', {
    title:       'Industry tile grid (live)',
    description: 'Auto-populated grid of industry tiles.',
    icon:        'grid-view',
    category:    'infer-nepal',
    supports:    { html: false, align: ['wide', 'full'] },
    attributes:  {
      count:   { type: 'number', default: 10 },
      columns: { type: 'number', default: 5 }
    },
    edit: function (props) {
      var a = props.attributes;
      var set = props.setAttributes;
      return el('div', { className: props.className },
        el(InspectorControls, {},
          el(PanelBody, { title: 'Industry grid settings', initialOpen: true },
            el(RangeControl, {
              label: 'How many tiles',
              value: a.count, min: 1, max: 20, step: 1,
              onChange: function (v) { set({ count: v }); }
            }),
            el(RangeControl, {
              label: 'Columns',
              value: a.columns, min: 1, max: 6, step: 1,
              onChange: function (v) { set({ columns: v }); }
            })
          )
        ),
        ServerSideRender
          ? el(ServerSideRender, { block: 'infer-nepal/industry-grid', attributes: a })
          : el('div', {}, 'Industry grid — preview unavailable')
      );
    },
    save: function () { return null; }
  });

  /* ============================================================
   * 3) Vendor stats (4 cards)
   * ============================================================ */
  registerBlockType('infer-nepal/vendor-stats', {
    title:       'Vendor stats (4 cards)',
    description: 'Four stat cards pulled from Customizer → Brand → Platform stats.',
    icon:        'chart-bar',
    category:    'infer-nepal',
    supports:    { html: false, align: ['wide', 'full'] },
    edit: function (props) {
      return el('div', { className: props.className, style: { background: '#333F4B', padding: '20px', borderRadius: '12px' } },
        ServerSideRender
          ? el(ServerSideRender, { block: 'infer-nepal/vendor-stats' })
          : el('div', { style: { color: '#fff' } }, 'Vendor stats')
      );
    },
    save: function () { return null; }
  });

  /* ============================================================
   * 4) Section heading (title + subtitle + link)
   * ============================================================ */
  registerBlockType('infer-nepal/section-heading', {
    title:       'Section heading',
    description: 'Big section title with optional subtitle and "see all" link.',
    icon:        'heading',
    category:    'infer-nepal',
    supports:    { html: false, align: ['wide', 'full'] },
    attributes:  {
      title:    { type: 'string', default: 'Top-rated software' },
      subtitle: { type: 'string', default: 'Most reviewed this quarter.' },
      linkText: { type: 'string', default: 'View all →' },
      linkUrl:  { type: 'string', default: '/software/' }
    },
    edit: function (props) {
      var a = props.attributes;
      var set = props.setAttributes;
      return el('div', { className: props.className },
        el(InspectorControls, {},
          el(PanelBody, { title: 'Heading settings', initialOpen: true },
            el(TextControl, { label: 'Title',    value: a.title,    onChange: function (v) { set({ title: v }); } }),
            el(TextControl, { label: 'Subtitle', value: a.subtitle, onChange: function (v) { set({ subtitle: v }); } }),
            el(TextControl, { label: 'Link text', value: a.linkText, onChange: function (v) { set({ linkText: v }); } }),
            el(TextControl, { label: 'Link URL',  value: a.linkUrl,  onChange: function (v) { set({ linkUrl: v }); } })
          )
        ),
        ServerSideRender
          ? el(ServerSideRender, { block: 'infer-nepal/section-heading', attributes: a })
          : el('div', {}, a.title)
      );
    },
    save: function () { return null; }
  });

})(window.wp);
