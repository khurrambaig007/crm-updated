import $ from './jquery-global';
import select2 from 'select2';

select2(window, $);

var select2Amd = $.fn.select2 && $.fn.select2.amd;
if (select2Amd && select2Amd.require) {
    var AjaxAdapter = select2Amd.require('select2/data/ajax');
    if (AjaxAdapter && AjaxAdapter.prototype._normalizeItem) {
        var originalNormalizeItem = AjaxAdapter.prototype._normalizeItem;
        AjaxAdapter.prototype._normalizeItem = function (item) {
            if (this && this.container) {
                return originalNormalizeItem.call(this, item);
            }
            return originalNormalizeItem.call(AjaxAdapter.prototype, item);
        };
    }
}
import 'datatables.net';
import 'datatables.net-responsive';
import './sweetalert';
import './phone-mask';
import './party';
import './shipper-bp';
import './purchase-invoice-grid';
import './container-activity-details-grid';
import './booking-details';
import './booking-equipment-grid';
import './booking-revenue-grid';
import './booking-cost-grid';
import './booking-totals';
import './booking-split';
import './company-onboarding';
import './container-purchase-tabs';
import './container-purchase';
