<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>$Subject</title>
</head>
<body>
<div id="EmailContent" style="margin: 20px">
    <table id="Content">
        <thead>
            <tr class="shopAddress">
                <th>
                    <% include Sunnysideup/Ecommerce/Includes/Order_ShopInfo_PackingSlip %>
                </th>
            </tr>

            <tr class="message">
                <td class="left">
                    <h1 class="title">$Subject</h1>
                    <% if OrderStepMessage %><div class="orderStepMessage">$OrderStepMessage.RAW</div><% end_if %>
                </td>
            </tr>

        </thead>
        <tbody>
            <tr>
                <td>
                    <% if Order %>
                        <% with Order %>
                            <div id="OrderInformation">
                                <h2 class="orderHeading"><% if RetrieveLink %><a href="$RetrieveLink"><% end_if %>$Title<% if RetrieveLink %></a><% end_if %></h2>
                                <% if Items %>
                                    <h2>Please place feedback for the following items:</h2>
                                    <ul>
                                    <% loop Items %>
                                        <li>
                                            <% if Link %>
                                                <a href="$Buyable.Link#Form_PageRatingForm" target="_blank">
                                                    $TableTitle
                                                </a>
                                            <% else %>
                                                <span class="tableTitle">$TableTitle</span>
                                            <% end_if %>
                                                <span class="tableSubTitle">$TableSubTitle</span>
                                        </li>
                                    <% end_loop %>
                                    </ul>
                                <% end_if %>
                                <% include Sunnysideup/Ecommerce/Includes/Order_Addresses %>
                            </div>
                        <% end_with %>
                    <% else %>
                        <p class="warning message">There was an error in retrieving this order. Please contact the store.</p>
                    <% end_if %>
                </td>
            </tr>
        </tbody>
    </table>

    <% if Order %>
        <% with Order %>
            <% if Status.MessageAfterProductsList %>
                <div class="messageAfterProductsList">$Status.MessageAfterProductsList</div>
            <% end_if %>
            <% if CopyOrderLink %>
                <a href="$CopyOrderLink" class="button reorder-button">
                    <% if $Status.LinkText %>
                        $Status.LinkText
                    <% else %>
                        Repeat this order
                    <% end_if %>
                </a>
            <% end_if %>
        <% end_with %>
    <% end_if %>
</div>
</body>
</html>
