/**
 * Qualyra — Custom Scrollbar (Alpine.js component)
 *
 * Replaces the native scrollbar with a floating pill-style thumb.
 * Supports: mouse wheel, drag, track click, touch.
 */
document.addEventListener('alpine:init', () => {
  Alpine.data('customScrollbar', (el) => ({
    // State
    scrollEl: el,
    _track: null,
    _thumb: null,
    isVisible: false,
    isDragging: false,
    isHovering: false,

    // Drag state
    dragStartY: 0,
    dragStartTop: 0,

    // Auto-hide timer
    hideTimer: null,

    // Thumb metrics
    trackHeight: 0,
    thumbHeight: 0,
    thumbTop: 0,

    init() {
      this._track = this.$refs.track;
      this._thumb = this.$refs.thumb;

      // Update on scroll
      this.scrollEl.addEventListener('scroll', () => this.onScroll(), { passive: true });

      // Update on resize
      this.ro = new ResizeObserver(() => this.updateMetrics());
      this.ro.observe(this.scrollEl);

      // Show on hover over the scroll container
      this.scrollEl.addEventListener('mouseenter', () => {
        this.isHovering = true;
        this.show();
      });

      this.scrollEl.addEventListener('mouseleave', () => {
        this.isHovering = false;
        if (!this.isDragging) this.scheduleHide();
      });

      // Global mouse events for drag
      document.addEventListener('mousemove', (e) => this.onDrag(e));
      document.addEventListener('mouseup', () => this.onDragEnd());

      // Initial calc
      this.$nextTick(() => this.updateMetrics());
    },

    destroy() {
      if (this.ro) this.ro.disconnect();
    },

    onScroll() {
      this.updateThumbPosition();
      this.show();
      this.scheduleHide();
    },

    updateMetrics() {
      const scrollH = this.scrollEl.scrollHeight;
      const clientH = this.scrollEl.clientHeight;

      if (scrollH <= clientH) {
        this.isVisible = false;
        return;
      }

      this.trackHeight = this._track.clientHeight;

      // Thumb height proportional to visible ratio, min 40px
      const ratio = clientH / scrollH;
      this.thumbHeight = Math.max(ratio * this.trackHeight, 40);

      this._thumb.style.height = `${this.thumbHeight}px`;

      this.updateThumbPosition();
    },

    updateThumbPosition() {
      const scrollH = this.scrollEl.scrollHeight;
      const clientH = this.scrollEl.clientHeight;

      if (scrollH <= clientH) return;

      const scrollRatio = this.scrollEl.scrollTop / (scrollH - clientH);
      const maxTop = this.trackHeight - this.thumbHeight;
      this.thumbTop = scrollRatio * maxTop;

      this._thumb.style.transform = `translateY(${this.thumbTop}px)`;
    },

    // ── Drag ──────────────────────────────────────────────

    onThumbMouseDown(e) {
      this.isDragging = true;
      this.dragStartY = e.clientY;
      this.dragStartTop = this.thumbTop;
      e.preventDefault();
    },

    onDrag(e) {
      if (!this.isDragging) return;

      const delta = e.clientY - this.dragStartY;
      const maxTop = this.trackHeight - this.thumbHeight;
      const newTop = Math.max(0, Math.min(maxTop, this.dragStartTop + delta));

      const scrollH = this.scrollEl.scrollHeight;
      const clientH = this.scrollEl.clientHeight;
      const scrollRatio = newTop / maxTop;

      this.scrollEl.scrollTop = scrollRatio * (scrollH - clientH);
    },

    onDragEnd() {
      this.isDragging = false;
      if (!this.isHovering) this.scheduleHide();
    },

    // ── Track click ───────────────────────────────────────

    onTrackClick(e) {
      if (e.target === this._thumb) return;

      const trackRect = this._track.getBoundingClientRect();
      const clickY = e.clientY - trackRect.top;
      const maxTop = this.trackHeight - this.thumbHeight;
      const targetTop = Math.max(0, Math.min(maxTop, clickY - this.thumbHeight / 2));

      const scrollH = this.scrollEl.scrollHeight;
      const clientH = this.scrollEl.clientHeight;
      const scrollRatio = targetTop / maxTop;

      this.scrollEl.scrollTo({
        top: scrollRatio * (scrollH - clientH),
        behavior: 'smooth',
      });
    },

    // ── Visibility ────────────────────────────────────────

    show() {
      clearTimeout(this.hideTimer);
      this.isVisible = true;
    },

    scheduleHide() {
      clearTimeout(this.hideTimer);
      this.hideTimer = setTimeout(() => {
        if (!this.isHovering && !this.isDragging) {
          this.isVisible = false;
        }
      }, 1200);
    },
  }));
});
