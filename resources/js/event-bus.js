const eventBus = {};

eventBus.on = function (event, callback) {
  this._events = this._events || {};
  this._events[event] = this._events[event] || [];
  this._events[event].push(callback);
};

eventBus.emit = function (event, data) {
  this._events = this._events || {};
  if (this._events[event]) {
    this._events[event].forEach(function (callback) {
      callback(data);
    });
  }
};

export default eventBus;
