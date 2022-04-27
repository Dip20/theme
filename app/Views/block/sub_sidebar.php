<!-- Sidemenu -->
<div class="main-sidebar main-sidebar-sticky side-menu">
    <div class="sidemenu-logo">
        <a class="main-logo" href="<?= url('') ?>">
            <img src="<?= LOGO; ?>" class="header-brand-img desktop-logo" alt="logo">
            <img src="<?= LOGOICON; ?>" class="header-brand-img icon-logo" alt="logo">
            <img src="<?= LOGO; ?>" class="header-brand-img desktop-logo theme-logo" alt="logo">
            <img src="<?= LOGOICON; ?>" class="header-brand-img icon-logo theme-logo" alt="logo">
        </a>
    </div>
    <div class="main-sidebar-body">
        <ul class="nav">
            <li class="nav-label">Dashboard</li>

            <li class="nav-item">
                <a class="nav-link with-sub" href=""><i class="fe fe-box"></i><span class="sidemenu-label">
                        Master</span><i class="angle fe fe-chevron-right"></i></a>
                <ul class="nav-sub">
                    <li class="nav-item ">
                        <a class="nav-link with-sub" href="#"><i class="fe fe-box"></i><span
                                class="sidemenu-label">Account</span>
                            <i class="angle fe fe-chevron-right"></i></a>
                        <ul class="nav-sub">
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('Account')?>">Add Account (Ledger) </a>
                            </li>
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('Master/glgrp')?>">General Ledger Group</a>
                            </li>
                        </ul>

                    <li class="nav-item ">
                        <a class="nav-link with-sub" href="#"><i class="fe fe-box"></i><span
                                class="sidemenu-label">Items</span>
                            <i class="angle fe fe-chevron-right"></i></a>
                        <ul class="nav-sub">
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('Items')?>">Item Entry</a>
                            </li>
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('Items/item_grp')?>">Item Group</a>
                            </li>
                        </ul>
                    </li>


                    <!-- <li class="nav-item ">
                        <a class="nav-link with-sub" href="#"><i class="fe fe-box"></i><span
                                class="sidemenu-label">Broker</span>
                            <i class="angle fe fe-chevron-right"></i></a>
                        <ul class="nav-sub">
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('master/broker')?>">Broker</a>
                            </li>
                        </ul>
                    </li> -->


                    <li class="nav-item ">
                        <a class="nav-link with-sub" href="#"><i class="fe fe-box"></i><span class="sidemenu-label">TDS
                                Rate</span>
                            <i class="angle fe fe-chevron-right"></i></a>
                        <ul class="nav-sub">
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('master/tds')?>">TDS Rate</a>
                            </li>
                        </ul>
                    </li>
                    <!-- ------------------------------ -->

                   
                    <li class="nav-item ">
                        <a class="nav-link with-sub" href="#"><i class="fe fe-box"></i><span
                                class="sidemenu-label">Others</span>
                            <i class="angle fe fe-chevron-right"></i></a>
                        <ul class="nav-sub">
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('master/billterm')?>">Billterm</a>
                            </li>
                            <!-- <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('master/bank')?>">Bank</a>
                            </li> -->
                           
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('master/warehouse')?>">Warehouse</a>
                            </li>
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?= url('master/transport') ?>">Transport</a>
                            </li>
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?= url('master/supervisor') ?>">Supervisor</a>
                            </li>
                           
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('master/uom')?>">UOM</a>
                            </li>
                          

                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?= url('master/vehicle') ?>">Vehicle</a>
                            </li>

                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?= url('master/hsn') ?>">HSN</a>
                            </li>

                          
                        </ul>
                    </li>
                </ul>
            </li>


            <li class="nav-item">
                <a class="nav-link with-sub" href=""><i class="fe fe-box"></i><span
                        class="sidemenu-label">Transactions</span><i class="angle fe fe-chevron-right"></i></a>

                <ul class="nav-sub">
                    <li class="nav-item ">
                        <a class="nav-link with-sub" href="#"><i class="fe fe-box"></i><span
                                class="sidemenu-label">Sales</span>
                            <i class="angle fe fe-chevron-right"></i></a>
                        <ul class="nav-sub">
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('sales/challan')?>">Item Challan</a>
                            </li>
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('sales/salesinvoice')?>">Item Invoice</a>
                            </li>
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('sales/salesreturn')?>">Item Return</a>
                            </li>
                           
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('sales/ac_invoice')?>">General Sales</a>
                            </li>

                        </ul>

                    <li class="nav-item ">
                        <a class="nav-link with-sub" href="#"><i class="fe fe-box"></i><span
                                class="sidemenu-label">Purchase</span>
                            <i class="angle fe fe-chevron-right"></i></a>
                        <ul class="nav-sub">
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('Purchase/purchasechallan')?>">Item Challan</a>
                            </li>
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('Purchase/purchaseinvoice')?>">Item Invoice</a>
                            </li>
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('Purchase/purchasereturn')?>">Item Return</a>
                            </li>
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('Purchase/general_purchase')?>">General
                                    Purchase</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item ">
                        <a class="nav-link with-sub" href="#"><i class="fe fe-box"></i><span class="sidemenu-label">Bank
                                /Cash</span>
                            <i class="angle fe fe-chevron-right"></i></a>
                        <ul class="nav-sub">
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('Bank/bank_transaction')?>">Bank Transaction</a>
                            </li>

                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('Bank/cash_transaction')?>">Cash Transaction</a>
                            </li>
                            
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('Bank/contra_transaction')?>">Contra Transaction</a>
                            </li>

                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('Bank/reconciliation')?>">Bank Reconciliation</a>
                            </li>

                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('Bank/unreconsilation')?>">Unlink Reconciliation</a>
                            </li>

                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('Bank/add_account_voucher')?>">Add Account Voucher</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item ">
                        <a class="nav-link with-sub" href="#"><i class="fe fe-box"></i><span class="sidemenu-label">JV
                                Particular</span>
                            <i class="angle fe fe-chevron-right"></i></a>
                        <ul class="nav-sub">
                            <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('bank/jv_particular')?>">Jv Paricular</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link with-sub" href=""><i class="fe fe-box"></i><span
                        class="sidemenu-label">Reporting</span><i class="angle fe fe-chevron-right"></i></a>

                <ul class="nav-sub">
                    <li class="nav-item">
                        <a class="nav-sub-link" href="<?=url('Trading/dashboard')?>">Trading</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-sub-link" href="<?=url('Trading/pl_dashboard')?>">Profit/Loss</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-sub-link" href="<?=url('Trading/balacesheet')?>">Balance Sheet</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-sub-link" href="<?=url('Trading/oc_closing')?>">Closing Balance</a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link with-sub" href=""><i class="fe fe-box"></i><span
                        class="sidemenu-label">Stock</span><i class="angle fe fe-chevron-right"></i></a>

                <ul class="nav-sub">
                    <li class="nav-item">
                        <a class="nav-sub-link" href="<?=url('Stock/item_stock')?>">Item Wise </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-sub-link" href="<?=url('Stock/group_wise_stock')?>">Group Item Wise </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-sub-link" href="<?=url('Stock/all_stock')?>">ALL</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-sub-link" href="<?=url('Stock/gray_stock')?>">Gray Stock </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-sub-link" href="<?=url('Stock/mill_stock')?>">Mill Stock </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-sub-link" href="<?=url('Stock/finish_stock')?>">Finish Stock </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-sub-link" href="<?=url('Stock/job_stock')?>">Jobwork Stock </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-sub-link" href="<?=url('Stock/recjob_stock')?>">Received Jobwork Stock </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-sub-link" href="<?=url('Stock/avg_price')?>">Average Price </a>
                    </li>
                </ul>

            </li>

            <li class="nav-label">Milling Entry</li>

            <li class="nav-item">
                <a class="nav-link with-sub" href=""><i class="fe fe-box"></i><span class="sidemenu-label">Gray/Finish
                        Purchase</span><i class="angle fe fe-chevron-right"></i></a>
                <ul class="nav-sub">
                    <li class="nav-sub-item">
                        <a class="nav-sub-link" href="<?=url('Milling/Grey_Challan')?>">Purchase Challan</a>
                    </li>

                    <li class="nav-sub-item">
                        <a class="nav-sub-link" href="<?=url('Milling/Grey_invoice')?>">Purchase Invoice</a>
                    </li>

                    <li class="nav-sub-item">
                        <a class="nav-sub-link" href="<?=url('Milling/retGrayFinish')?>">Purchase Return</a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link with-sub" href=""><i class="fe fe-box"></i><span class="sidemenu-label">Gray/Finish
                        Sale</span><i class="angle fe fe-chevron-right"></i></a>
                <ul class="nav-sub">
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('milling/mill_sale_challan')?>">Sale
                            Challan</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('milling/mill_sale_invoice')?>">Sale
                            Invoice</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('milling/mill_sale_return')?>">Sale Return</a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link with-sub" href=""><i class="fe fe-box"></i><span class="sidemenu-label">Mill</span><i
                        class="angle fe fe-chevron-right"></i>
                </a>
                <ul class="nav-sub">
                    <li class="nav-sub-item">
                        <a class="nav-sub-link" href="<?=url('Milling/mill_challan')?>">Mill Issue</a>
                    </li>

                    <li class="nav-sub-item">
                        <a class="nav-sub-link" href="<?=url('Milling/mill_rec')?>">Mill Received</a>
                    </li>

                    <li class="nav-sub-item">
                        <a class="nav-sub-link" href="<?=url('Milling/return_mill')?>">Mill Return</a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link with-sub" href=""><i class="fe fe-box"></i><span
                        class="sidemenu-label">Jobwork</span><i class="angle fe fe-chevron-right"></i>
                </a>
                <ul class="nav-sub">
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Milling/Jobwork')?>">Jobwork Issue</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Milling/Jobwork_rec')?>">Jobwork Received</a>
                    </li>

                    <li class="nav-sub-item">
                        <a class="nav-sub-link" href="<?=url('Milling/return_jobwork')?>">Jobwork Return</a>
                    </li>
                </ul>
            </li>
            
            <li class="nav-label">Milling Reports</li>

            <li class="nav-item">
                <a class="nav-link with-sub" href=""><i class="fe fe-box"></i><span class="sidemenu-label">Short
                        Reports</span><i class="angle fe fe-chevron-right"></i></a>

                <ul class="nav-sub">
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/gray_item_wise')?>">Gray Item Wise
                            Report</a>
                    </li>
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/Gray_Invoice_wise')?>">Gray Invoice
                            Wise Report</a>
                    </li>

                    <!-- <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/gray_issue_report')?>">Gray Issue
                            Report</a>
                    </li> -->

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/gray_return_report')?>">Gray Return
                            Report</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/Mill_report')?>">Mill Issue Voucher
                            Report</a>
                    </li>
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/sendMill_item_wise')?>">Mill Issue
                            Item Report</a>
                    </li>

                    <!-- <li class="nav-sub-item ">
                                <a class="nav-sub-link" href="<?=url('MillingReport/mill_rec_report')?>">Mill Received Sceen Report</a>
                            </li> -->

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/RecMill_report')?>">Mill Received
                            Voucher Report</a>
                    </li>
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/recMill_item_wise')?>">Mill Received
                            Item Report</a>
                    </li>


                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/sendJob_report')?>">Job Issue
                            Voucher Report</a>
                    </li>

                    <li class="nav-sub-item">
                        <a class="nav-sub-link" href="<?=url('MillingReport/sendJob_item_wise')?>">Job Issue
                            Item Report</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/RecJob_report')?>">Job Received
                            Voucher Report</a>
                    </li>
                </ul>
            </li>


            <li class="nav-item">
                <a class="nav-link with-sub" href=""><i class="fe fe-box"></i><span class="sidemenu-label">Item Wise
                        Report</span><i class="angle fe fe-chevron-right"></i></a>

                <ul class="nav-sub">
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/gray_issue_ItemWise_report')?>">Gray Issue
                            Item Wise</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/gray_return_ItemWise_report')?>">Gray Return
                            Item Wise</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/finish_issue_ItemWise_report')?>">Finish Issue
                            Item Wise</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/finish_return_ItemWise_report')?>">Finish Return
                            Item Wise</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/mill_issue_ItemWise_report')?>">Mill Issue
                            Item Wise</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/mill_return_ItemWise_report')?>">Mill Return
                            Item Wise</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/mill_received_ItemWise_report')?>">Mill
                            Received Item Wise</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/job_issue_ItemWise_report')?>">Job Issue
                            Item Wise</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/job_return_ItemWise_report')?>">Job Return
                            Item Wise</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/job_received_ItemWise_report')?>">Job
                            Received Item Wise</a>
                    </li>
                </ul>
            </li>

            <li class="nav-item ">
                <a class="nav-link with-sub" href="#"><i class="fe fe-box"></i><span class="sidemenu-label">Broker Wise
                        Report</span>
                    <i class="angle fe fe-chevron-right"></i></a>
                <ul class="nav-sub">

                    <li class="nav-sub-item">
                        <a class="nav-sub-link" href="<?=url('MillingReport/gray_issue_BrokerWise_report')?>">Gray Issue
                            Broker Wise</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/gray_return_BrokerWise_report')?>">Gray
                            Return Broker Wise</a>
                    </li>

                    <li class="nav-sub-item">
                        <a class="nav-sub-link" href="<?=url('MillingReport/finish_issue_BrokerWise_report')?>">Finish Issue
                            Broker Wise</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/finish_return_BrokerWise_report')?>">Finish
                            Return Broker Wise</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/mill_issue_BrokerWise_report')?>">Mill Issue
                            Broker Wise</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/mill_return_BrokerWise_report')?>">Mill
                            Return Broker Wise</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/mill_received_BrokerWise_report')?>">Mill
                            Received Broker Wise</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/job_issue_BrokerWise_report')?>">Job Issue
                            Broker Wise</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/job_return_BrokerWise_report')?>">Job Return
                            Broker Wise</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/job_received_BrokerWise_report')?>">Job
                            Received Broker Wise</a>
                    </li>
                </ul>
            </li>

            <li class="nav-item ">
                <a class="nav-link with-sub" href="#"><i class="fe fe-box"></i><span class="sidemenu-label">Party Wise
                        Report</span>
                    <i class="angle fe fe-chevron-right"></i></a>
                <ul class="nav-sub">
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/gray_issue_report')?>">Gray Issue
                            Report</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/gray_return_report')?>">Gray Return
                            Report</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/mill_issue_report')?>">Mill Issue
                            Report</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/mill_return_report')?>">Mill Return
                            Report</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/mill_received_report')?>">Mill
                            Received Report</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/job_issue_report')?>">Job Issue
                            Report</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/job_return_report')?>">Job Return
                            Report</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/job_received_report')?>">Job
                            Received Report</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/finish_issue_report')?>">Finish
                            Issue Report</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('MillingReport/finish_return_report')?>">Finish
                            Return Report</a>
                    </li>
                </ul>
            </li>

            <li class="nav-label">Register Reports</li>

            <li class="nav-item">
                <a class="nav-link with-sub" href=""><i class="fe fe-box"></i><span class="sidemenu-label">Account
                        Book</span><i class="angle fe fe-chevron-right"></i></a>
                <ul class="nav-sub">

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/Sales_register');?>">Sales Register</a>
                    </li>
                    
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/Sales_gst_register');?>">Sales GST Register</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/Gnrl_sales_register');?>">General Sales Register</a>
                    </li>
                    
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/Gnrl_sales_gst_register');?>">General Sales GST Register</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/Purchase_register')?>">Purchase Register</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/Purchase_gst_register');?>">Purchase GST Register</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/Gnrl_purchase_register');?>">General Purchase Register</a>
                    </li>
                    
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/Gnrl_purchase_gst_register');?>">General Purchase GST Register</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/Sales_return_register')?>">Credit Note Register</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/Creditnote_gst_register')?>">Credit Note GST Register</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/Purchase_return_register')?>">Debit Note Register</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/Debitnote_gst_register')?>">Debit Note GST Register</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/payment')?>">Payment Register</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/receipt')?>">Receipts Register</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/contra')?>">Contra Register</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/cash')?>">Cash Register</a>
                    </li>
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/bank')?>">Bank Register</a>
                    </li>
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/journal')?>">Journal Register</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/ledger')?>">Ledger Register</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/Groupsummary')?>">Group Summary</a>
                    </li>

                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/Outstanding/receivable')?>">Receivable</a>
                    </li>
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/Outstanding/payable')?>">Payable</a>
                    </li>
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('Addbook/Ledger_outstanding')?>">Ledger Outstandig</a>
                    </li>

                </ul>
            </li>


            <li class="nav-label">GST</li>

            <li class="nav-item">
                <a class="nav-link with-sub" href=""><i class="fe fe-box"></i><span class="sidemenu-label">GST</span><i
                        class="angle fe fe-chevron-right"></i></a>
                <ul class="nav-sub">
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('gst/gstr1');?>">GSTR 1</a>
                    </li>
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('gst/gstr2');?>">GSTR 2</a>
                    </li>
                    <li class="nav-sub-item ">
                        <a class="nav-sub-link" href="<?=url('gst/gstr3');?>">GSTR 3</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- End Sidemenu -->