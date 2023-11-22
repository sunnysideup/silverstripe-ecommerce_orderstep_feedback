<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>$Subject</title>
</head>
<body>
<div id="EmailContent" style="padding: 20px; max-width: 500px; margin: 0 auto;">
    <% if $Order %>
        <% with $Order %>
        Dear $Member.FirstName,<br /><br />
        <% end_with %>

        <% if $OrderStepMessage %><div class="orderStepMessage">$OrderStepMessage</div><% end_if %>

        <% with Order %>
            <hr />
            <p>
            Order Reference: <% if $RetrieveLink %><a href="$RetrieveLink"><% end_if %>$Title<% if RetrieveLink %></a><% end_if %>
            <% if $CopyOrderLink %>
            <br />
                <a href="$CopyOrderLink" class="button reorder-button">
                    <% if $Status.LinkText %>
                        $Status.LinkText
                    <% else %>
                        Repeat this order
                    <% end_if %>
                </a>
            <% end_if %>
            </p>
            <hr />

            <% if Status.MessageAfterProductsList %>
                <div class="messageAfterProductsList">$Status.MessageAfterProductsList</div>
            <% end_if %>

        <% end_with %>
    <% end_if %>
</div>
</body>
</html>
