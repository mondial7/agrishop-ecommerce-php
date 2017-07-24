class AgriProduct {

  constructor() {



  }

  add () {


    var quantity = document.getElementById('quantiy')
    var category = document.getElementById('category')
    var area = document.getElementById('area')
    var price = document.getElementById('price')

    url = 'api/prodcuts/add?quantity=' + quantity

    OldWheel.get({ url }, response => {

      console.log(response)

      if (JSON.parse(response).status === "OK") {

        alert('ok')

        // reset form
        // ...

      }

    })

  }

}

/**
 * *** **** ***** ****** ******* ****** ***** **** *** **** ***** ****** *******
 * *** **** ***** ****** ******* ****** ***** **** *** **** ***** ****** *******
 * *** **** ***** ****** ******* ****** ***** **** *** **** ***** ****** *******
 *
 * Agrishop Options
 *
 */
class AgriOption {

  constructor () {

    this.old_password_input = document.getElementById('input_old_password')
    this.new_password_input = document.getElementById('input_new_password')

    this.activate_farm_lock = false

  }

  /**
   * Save an element
   * The http calls are made in POST
   * for all the option api
   * NOTE use this method only for single input field
   *
   */
  save (name) {

    const elem = document.getElementById('input_' + name)
    const saver = document.getElementById('saver_button_' + name)
    const value = elem.value

    let lock = false
    let isFarmItem = false
    let url = ''
    let parameters = ''

    // start loading visual feedback
    saver.className = 'general_saver loading'

    // set url and parameters according to name
    switch (name) {

      case 'new_password':

        const old_password = this.old_password_input.value
        const new_password = this.new_password_input.value

        if (old_password == '' || new_password == '') {

          alert('You cannot save an empty password')

          // exit
          saver.className = 'general_saver'
          return

        }

        url = 'api/options/account/'
        parameters = 'o=update&f=password&v=' + new_password + '&c=' + old_password

        break;

      case 'username':

        url = 'api/options/account/'
        parameters = 'o=update&f=username&v=' + value

        break;

      case 'mail':

        url = 'api/options/account/'
        parameters = 'o=update&f=email&v=' + value

        break;

      case 'farm_name':

        url = 'api/options/managefarm/'
        parameters = 'o=update&f=name&v=' + value
        isFarmItem = true

        break;

      case 'farm_owner_name':

        url = 'api/options/managefarm/'
        parameters = 'o=update&f=owner_name&v=' + value
        isFarmItem = true

        break;

      case 'farm_owner_surname':

        url = 'api/options/managefarm/'
        parameters = 'o=update&f=owner_surname&v=' + value
        isFarmItem = true

        break;

      default:

        console.warn('No match with saver name')

        // exit
        saver.className = 'general_saver'
        return

    }

    // look for empty values
    // prevent user errors
    if (value === '') {

        lock = confirm("You are trying to save an empty data, do you want to proceed?")

    } else {

        lock = true

    }

    // save lock to prevent
    // user to save an empty value
    if (lock) {

      if (isFarmItem) {

        url = url + '?' + parameters

        OldWheel.get({ url }, response => {

          this.saveCallback(response, elem, value, saver, name)

        })

      } else {

        OldWheel.post({ url, parameters }, response => {

          this.saveCallback(response, elem, value, saver, name)

        })

      }

    } else {

      saver.className = 'general_saver'

    }

  }

  /**
   * Callback for standard save (single field)
   *
   */
  saveCallback (response, elem, value, saver, name) {

    try {

      const data = JSON.parse(response)

      if (data.status === 'OK') {

        // set back the saver
        saver.className = 'general_saver'

        if (name != 'new_password') {

          // update new placeholder values
          this.update(elem, value)

        } else {

          // empty password inputs
          this.old_password_input.value = ''
          this.new_password_input.value = ''

        }

        // nice end
        alert('Saved')

      } else {

        saver.className = 'general_saver active'

        console.warn(response)

        if (data.error) {

          if (name == 'new_password') {

            alert('Sorry, it was not possible to set the new password.')

          } else {

            alert(data.error)

          }

        }

      }

    } catch (e) {

      console.warn(e.toString() + response)

    }

  }

  /**
   * Set the saver button to active
   *
   */
  prepare (name) {

    let saver = document.getElementById('saver_button_' + name)

    // set saver button to active
    saver.className = 'general_saver active'

  }

  /**
   * Assign placeholder value to real value
   *
   */
  edit (elem) {

    // set placeholder to value
    elem.setAttribute('value', elem.placeholder)
    // set color for a visual feedback
    elem.setAttribute('style', 'color:#222')

  }

  /**
   * Update input with the new value
   *
   */
  update (elem, value) {

    elem.setAttribute('value', value)
    elem.setAttribute('placeholder', value)
    // set color for a visual feedback
    elem.setAttribute('style', 'color:#777')

  }

  /**
   * Prepare password input
   * manager both old and new password inputs
   * as we do in 'prepare()' set the saver
   * button to active
   *
   */
  prepare_password () {

    const old_password = this.old_password_input.value
    const new_password = this.new_password_input.value
    const saver = document.getElementById('saver_button_new_password')

    if (old_password !== '' && new_password !== '') {

      // set saver button to active
      saver.className = 'general_saver active'

    } else {

      // disable saver button
      saver.className = 'general_saver'

    }

  }

  /**
   * Activate farm
   */
  activateFarm () {

    if (this.activate_farm_lock) {

      return

    }

    // lock to prevent double call
    this.activate_farm_lock = true

    // get data
    const name = document.getElementById('activate_farm_name')
    const owner_name = document.getElementById('activate_farm_owner_name')
    const owner_surname = document.getElementById('activate_farm_owner_surname')

    // send xhr call
    let url = 'api/options/managefarm/?o=add&name' + name + '&owner_name=' + owner_name + '&owner_surname=' + owner_surname

    OldWheel.get({ url }, response => {

      // release lock
      this.activate_farm_lock = false

      const data = JSON.parse(response)

      if (data.status === 'OK') {

        alert('Farm activated')

        // reload page
        window.location.reload()

      } else {

        console.warn(response)

      }

    })

  }

}


/**
 * *** **** ***** ****** ******* ****** ***** **** *** **** ***** ****** *******
 * *** **** ***** ****** ******* ****** ***** **** *** **** ***** ****** *******
 * *** **** ***** ****** ******* ****** ***** **** *** **** ***** ****** *******
 *
 * Agrishop Cart
 *
 */
class AgriCart {

  constructor () {

    this.modal = document.getElementsByClassName('cart__modal')[0]
    this.trigger = document.getElementById('cart_trigger')
    this.container = document.getElementsByClassName('cart__wrapper')[0]
    this.footer = document.getElementsByClassName('cart__footer')[0]

    this.items_ = []
    this.loadStatus()

  }

  get items () { return this.items_ }

  loadStatus () {

    let url = 'api/cart/status/'
    OldWheel.get({ url }, response => {

      try {

        let data = JSON.parse(response)

        if (data.error === 'not logged') {

          this.items_ = []

        } else {

          this.items_ = data

        }

      } catch (e) {

        console.warn(e.toString())
        console.log(response)

      }

    })

  }

  checkout () {}

  /**
   * Display cart modal and fill it with items
   *
   */
  show () {

    // send request to retrieve cart items
    this.fill();

    // display cart modal
    this.modal.classList.add('cart__modal--visible')

    // while showing the modal hide the main to avoid scroll
    document.getElementsByTagName('main')[0].classList.add('collapsed')

    // substitute the trigger to simulate a toggle button
    this.trigger.setAttribute('href', 'javascript:agri.cart.hide()')

  }

  /**
   * Hide cart modal
   *
   */
  hide () {

    // display cart modal
    this.modal.classList.remove('cart__modal--visible')

    // while showing the modal hide the main to avoid scroll
    document.getElementsByTagName('main')[0].classList.remove('collapsed')

    // toggle trigger
    this.trigger.setAttribute('href', 'javascript:agri.cart.show()')

  }

  /**
   * Fill the cart with items
   *
   */
  fill () {

    // empty container
    this.container.innerHTML = ''

    let url = 'api/cart/status/'

    OldWheel.get({ url }, response => {

      if (response === '{"error":"not logged"}') {

        console.log("Not logged")

        // display a login button
        this.container.innerHTML = '<span><a class="button--link" href="login/">Login First</a></span>'

        // hide checkout
        this.footer.style.display = 'none'

      } else {

        // render the response in the cart__wrapper
        this.render(JSON.parse(response))

      }

    })

  }

  /**
   * Render cart items
   *
   */
  render (product_ids) {

    product_ids.forEach(id => {

      // get product info and append to the cart
      let url = 'api/search/product/?id=' + id
      OldWheel.get({ url }, (data) => {

        try {

          let product = JSON.parse(data)

          // build html string
          let node_html = `<section class="product__card product__card--cart" id="product_cart_${product.id}">
            <div class="product__picture">
              <img src="app/assets/pics/icons/${product.category}.svg" />
            </div>
            <div class="product__info">
              <p class="product__name">${product.name}</p>
              <p class="product__quantity">${product.quantity} l/kg</p>
              <p class="product__price">€ ${product.price}</p>
            </div>
            <div class="product__action">
              <p class="product__cart_button" onclick="agri.cart.remove(${product.id})">Remove</p>
            </div>
          </section>`

          // append to container
          this.container.insertAdjacentHTML('beforeend', node_html)

        } catch (e) {

          console.warn(e.toString() + ' ' + data)

        }

      })

    })

  }

  /**
   * Add element to the cart
   *
   */
  add (id) {

    // add element to cart
    let url = 'api/cart/update/?o=add&i=' + id
    OldWheel.get({ url }, response => {

      let data = JSON.parse(response)

      if (data.status === 'OK') {

        // remove element from view
        let product = document.getElementById('product_' + id)
        product.parentNode.removeChild(product)

      } else if (data.error == 'not logged') {

        alert('You should login first')

      } else {

        console.log(response)

      }

    })

  }

  /**
   * Remove element from the cart
   *
   */
  remove (id) {

    // remove from cart
    let url = 'api/cart/update/?o=remove&i=' + id
    OldWheel.get({ url }, response => {

      if (JSON.parse(response).status === 'OK') {

        // remove element from view
        let product = document.getElementById('product_cart_' + id)
        product.parentNode.removeChild(product)

        // update cart items
        this.loadStatus()

        // refresh search
        if (typeof searchpage !== 'undefined' && searchpage) {

          agri.search.commit()

        }

      } else {

        console.log(response)

      }

    })

  }

  clean () {

    // remove everything from cart
    let url = 'api/cart/update/?o=empty&i=1'
    OldWheel.get({ url }, response => {

      if (JSON.parse(response).status === 'OK') {

        // update cart items
        this.loadStatus()

        // refresh search
        if (typeof searchpage !== 'undefined' && searchpage) {

          agri.search.commit()

        }

        // hide cart
        this.hide()

      } else {

        console.log(response)

      }

    })

  }

}

/**
 * *** **** ***** ****** ******* ****** ***** **** *** **** ***** ****** *******
 * *** **** ***** ****** ******* ****** ***** **** *** **** ***** ****** *******
 * *** **** ***** ****** ******* ****** ***** **** *** **** ***** ****** *******
 *
 * Agrishop Search
 *
 */
class AgriSearch {

  constructor () {

    // wrapper to the products' list container
    this.wrapper = document.getElementsByClassName('product__wrapper')[0]

    // filters
    this.filters = ''
    this.sortOrder = 1
    this.sortSelect = document.getElementsByClassName('filter__order__select')[0]
    this.sortArrow = document.getElementsByClassName('filter__order__arrow')[0]
    this.searchBar = document.getElementsByClassName('filter__searchbar__input')[0]
    this.areas = document.getElementsByName('areas')
    this.categories = document.getElementsByName('categories')

  }

  /**
   * Perform search (ajax call)
   *
   */
  commit () {

    // collect filters
    this._updateFilters()

    // get call to retrieve results
    // callback should provide html results
    // or a json error
    const url = 'api/search/product/?' + this.filters

    OldWheel.get({ url }, response => {

      // try to parse the response body
      try {

        const data = JSON.parse(response)

        // look up and alert errors
        if (data.error) {

          alert(data.error)

        } else {

          // check if there are any result
          if (data.length <= 0) {

            this.renderPlaceholder()

          } else {

            // render results
            this.render(data)

          }

        }

      } catch (e) {

        console.warn('error: ' + e.toString())

      }

    })

  }

  /**
   * render products list
   *
   * @param array of products
   */
  render (products) {

    const container = document.createElement('div')
    container.className = 'product__container'

    // loop through the array object
    products.forEach(product => {

      // exclude products in the cart
      let in_cart = agri.cart.items.find(x => x == product.id)

      if (typeof in_cart !== 'undefined') {

        return

      }

      // patch for description
      if (typeof product.description === 'undefined') {

        product.description = ''

      }

      // build html string
      let node_html = `<section class="product__card" id="product_${product.id}">
    		<div class="product__picture">
    			<img src="app/assets/pics/icons/${product.category}.svg" />
    		</div>
    		<div class="product__info">
    			<p class="product__name">${product.name}</p>
    			<p class="product__quantity">${product.quantity} l/kg</p>
    			<p class="product__price">€ ${product.price}</p>
    			<p class="product__date">${product.produced}</p>
    		</div>
    		<div class="product__geoinfo">
    			<p class="product__farm" data-farm-id="${product.farm_id}">${product.farm}</p>
    			<p class="product__category" data-category-id="${product.category_id}">${product.category}</p>
    			<p class="product__area" data-area-id="${product.area_id}">${product.area}</p>
    		</div>
    		<div class="product__extrainfo">
    			<p class="product__description">${product.description}</p>
    		</div>
    		<div class="product__action">
    			<p class="product__cart_button" onclick="agri.cart.add(${product.id})">Add To Cart</p>
    		</div>
    	</section>`

      // append to container
      container.insertAdjacentHTML('beforeend', node_html)

    })

    // empty wrapper
    this.wrapper.innerHTML = ''
    // append new container to the document
    this.wrapper.appendChild(container)

  }

  /**
   * Fill a card with no results to show as placeholder
   *
   *
   */
  renderPlaceholder () {

    const container = document.createElement('div')
    container.className = 'product__container'

    let placeholders = ['a','b','c']

    // loop through the array object
    placeholders.forEach(() => {

      // build html string
      let node_html = `<section class="product__card">
    		<div class="product__picture">
    			<img src="app/assets/pics/icons/logo.svg" />
    		</div>
    		<div class="product__info">
    			<p class="product__name">No results</p>
    		</div>
    		<div class="product__geoinfo">
    			<p class="product__farm"></p>
    		</div>
    		<div class="product__extrainfo">
    			<p class="product__description"></p>
    		</div>
    		<div class="product__action">
    			<p class="product__cart_button">...</p>
    		</div>
    	</section>`

      // append to container
      container.insertAdjacentHTML('beforeend', node_html)

    })

    // empty wrapper
    this.wrapper.innerHTML = ''
    // append new container to the document
    this.wrapper.appendChild(container)

  }

  /**
   * Collect current filters values
   *
   */
  _updateFilters () {

    let filter = ""

    filter += "c="
    for (let i=0; i < this.categories.length; i++) {
      if(this.categories[i].checked == true){

        filter += this.categories[i].value + "_"

      }
    }

    filter += "&a="
    for(let i=0; i < this.areas.length; i++){
      if(this.areas[i].checked == true){

        filter += this.areas[i].value + "_"

      }
    }

    filter += "&s=" + this.searchBar.value

    // store filters
    this.filters = filter

  }

  /**
   * Commit category
   * toggle layout of clicked button
   *
   */
  commit_category (elem) {

    const attr = 'data-checked'
    const img = document.getElementById('img_' + elem.id)

    if (elem.getAttribute(attr) === 'true') {

      elem.setAttribute(attr, 'false')
      img.classList.remove('checked')

    } else {

      elem.setAttribute(attr, 'true')
      img.classList.add('checked')

    }

    // call the search commit
    this.commit()

  }

  /**
   * Filters quick selection all/none
   *
   * @param string name of target filter
   * @param string action true/false
   */
  quickselect (target, action) {

    let nodes

    if (target === 'areas') {

      nodes = this.areas

    } else if (target === 'categories') {

      nodes = this.categories

    } else {

      return

    }

    nodes.forEach(node => {

      node.setAttribute('checked', action)

    })

  }

  /**
   * Sort the products results according to ordering filter
   *
   */
  sort () {

    // ...

    // check if order is the reverse
    if (this.sortOrder === 0) {

      // ...

    }

  }

  /**
   * Reverse the sorting order
   *
   */
  reverseSort (elem) {

    const value = elem.getAttribute('data-value')

    if (value === 'down') {

      this.sortOrder = 1

    } else if (value === 'top') {

      this.sortOrder = 0

    }

  }

}

/**
 * *** **** ***** ****** ******* ****** ***** **** *** **** ***** ****** *******
 * *** **** ***** ****** ******* ****** ***** **** *** **** ***** ****** *******
 * *** **** ***** ****** ******* ****** ***** **** *** **** ***** ****** *******
 *
 * Agrishop
 *
 */
class Agrishop {

  constructor () {

    this.search = new AgriSearch()
    this.cart = new AgriCart()
    this.option = new AgriOption()

  }

}


let agri = new Agrishop()

/**
 * Trigger search if in the search page
 */
if (typeof searchpage !== 'undefined' && searchpage) {

  agri.search.commit()

}
