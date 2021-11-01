import request from '@/utils/request';
import pluralize from 'pluralize';

/**
 * Simple RESTful resource class
 */
class Resource {
  constructor(uri) {
    this.uri = uri;
    this.singularUri = pluralize.singular(uri);
  }
  list(query) {
    return request({
      url: '/' + this.uri,
      method: 'get',
      params: query,
    });
  }
  get(id, query = {}) {
    return request({
      url: '/' + this.uri + '/' + id,
      method: 'get',
      params: query,
    });
  }
  store(resource) {
    return request({
      url: '/' + this.singularUri,
      method: 'post',
      data: resource,
    });
  }
  update(id, resource) {
    return request({
      url: '/' + this.singularUri + '/' + id,
      method: 'patch',
      data: resource,
    });
  }
  destroy(id) {
    return request({
      url: '/' + this.singularUri + '/' + id,
      method: 'delete',
    });
  }
}

export { Resource as default };
