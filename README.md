CT-Duplicate-Order
==================

Allow customers to duplicate previous orders in a Cart Throb store.

Thanks to Rob Sanchez for setting us off on the right track (http://expressionengine.stackexchange.com/questions/23719/creating-repeat-orders-in-cartthrob-tips-practices).

Summary
=======

This plugin queries the exp_cartthrob_order_items table in the EE database, specifically targeting the 'extra' field. This stores all the order options within a serialized and Base64 encoded array. Each line item is extracted as an array and the key/value pairs can be used in hidden form fields within a {exp:cartthrob:multi_add_to_cart_form} tag pair.

Template Usage
==============

```html
<h2>My Orders</h2>
{exp:channel:entries channel="orders" status="not none" dynamic="no" author_id="CURRENT_USER"}

{if no_results}
<p>Sorry, you don’t appear to have placed any previous orders.</p>
{/if}

{if count == '1'}
<table>
<caption>My Order History</caption>
<thead>
  <td>Order Number</td>
  <td>Date Placed</td>
  <td>Order Total</td>
  <td>Status</td>
  <td>&nbsp;</td>
</thead>
<tbody>
{/if}

 <tr>
    <td><a href="/account/orders/{url_title}">{title}</a></td>
    <td>{entry_date format="%l %j%S %F %Y at %g:%i%a"}</td>
    <td>{order_total}</td>
    <td>{status}</td>
    <td>
    {exp:cartthrob:multi_add_to_cart_form return="basket/index"}
      {exp:cartthrob:order_items order_id="{entry_id}" variable_prefix="item:"}
			<input type="hidden" name="entry_id[{item:row_order}]" value="{item:entry_id}" />
      <input type="hidden" name="quantity[{item:row_order}]" value="{item:quantity}" />
				{exp:repeat_order order_id="{entry_id}" row_order="{item:row_order}"}
				<input type="hidden" name="item_options[{item:row_order}][{repeat_order_key}]" value="{repeat_order_value}" />
				{/exp:repeat_order}
			{/exp:cartthrob:order_items}
    <input type="submit" value="Repeat this order" />
    {/exp:cartthrob:multi_add_to_cart_form}
    </td>
  </tr>
  {if count == total_results}
  </tbody>
</table>
{/if}
{/exp:channel:entries}
```
